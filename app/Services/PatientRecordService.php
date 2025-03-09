<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Patient;
use App\Models\PatientFile;
use App\Presenters\FilePresenter;

class PatientRecordService
{
    protected $filePresenter;

    public function __construct(FilePresenter $filePresenter)
    {
        $this->filePresenter = $filePresenter;
    }

    public function getPatientRecordsPath($patientId)
    {
        return storage_path("app/public/patient_records/{$patientId}");
    }

    public function listFiles($directory, $keyword = '', $isInsecure = false)
    {
        if (!is_dir($directory)) {
            return [];
        }

        if ($isInsecure) {
            $command = empty($keyword)
                ? "ls -1 {$directory} 2>&1"
                : "ls -1 {$directory} | grep -i {$keyword} 2>&1";

            return array_filter(explode("\n", shell_exec($command) ?? ''), 'strlen');
        } else {
            if (preg_match('/[;|&$><`\]}\.\/]/', $keyword)) {
                throw new \Exception('Invalid characters detected in search query.');
            }

            $safeKeyword = preg_replace('/[^a-zA-Z0-9_-]/', '', $keyword);

            $files = scandir($directory);
            if (!$files) {
                return [];
            }

            return array_filter($files, function ($file) use ($safeKeyword) {
                return stripos($file, $safeKeyword) !== false;
            });
        }
    }

    public function searchRecords($patientId, $keyword = '')
    {
        $patient = Patient::find($patientId);
        if (!$patient) {
            return ['error' => 'Patient not found.'];
        }

        $isInsecure = auth()->user()->cmd_inject_on ?? false;

        try {
            $recordsPath = $this->getPatientRecordsPath($patient->patient_id);
            $fileList = $this->listFiles($recordsPath, $keyword, $isInsecure);
            $downloadLinks = $this->filePresenter->generateDownloadLinks($fileList, $patient->patient_id);

            return [
                'patientInfo' => $patient,
                'patientFiles' => count($downloadLinks) ? implode('<br>', $downloadLinks) : 'No files found.',
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function storeRecord($patientId, $file)
    {
        $patient = Patient::find($patientId);
        if (!$patient) {
            throw new \Exception('Patient not found.');
        }

        $isInsecure = auth()->user()->file_upload_on ?? false;

        if (!$isInsecure) {
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];
            $maxFileSize = 5 * 1024 * 1024;
            $fileExtension = strtolower($file->getClientOriginalExtension());
            $fileSize = $file->getSize();

            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new \Exception('Invalid file type. Allowed types: PDF, JPG, PNG, and DOCX.');
            }

            if ($fileSize > $maxFileSize) {
                throw new \Exception('File too large. Maximum size allowed is 5MB.');
            }
        }

        $filePath = $this->storeFile($patient->patient_id, $file);

        return PatientFile::create([
            'patient_id' => $patient->patient_id,
            'filename'   => $file->getClientOriginalName(),
            'path'       => $filePath
        ]);
    }

    protected function storeFile($patientId, $file)
    {
        $directory = "patient_records/{$patientId}";
        Storage::makeDirectory($directory);

        $fileName = $file->getClientOriginalName();
        return $file->storeAs($directory, $fileName, 'public');
    }
}
