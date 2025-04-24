<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Patient;
use App\Models\PatientFile;
use App\Presenters\FilePresenter;

class PatientRecordService
{
    protected $filePresenter;
    private const MAX_FILE_SIZE_BYTES = 5 * 1024 * 1024;

    public function __construct(FilePresenter $filePresenter)
    {
        $this->filePresenter = $filePresenter;
    }

    public function getPatientRecordsPath($patientId)
    {
        return storage_path("app/patient_records/{$patientId}");
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
        }

        if (preg_match('/[;|&$><`\]}\.\/]/', $keyword)) {
            throw new \Exception('Invalid characters detected in search query.');
        }

        $safeKeyword = preg_replace('/[^a-zA-Z0-9_-]/', '', $keyword);

        $files = scandir($directory);
        $files = array_diff($files, ['.', '..']);
        if (!$files) {
            return [];
        }

        return array_filter($files, function ($file) use ($safeKeyword) {
            return stripos($file, $safeKeyword) !== false;
        });
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

        $isSecure = !(auth()->user()->file_upload_on ?? false);

        if ($isSecure) {
            $allowedExtensions = [
                'csv', 'xlsx', 'json', 'edi', 'xml', 'pdf', 'txt'
            ];
            $allowedMimeTypes = [
                'text/csv',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/json',
                'application/EDI-X12',
                'application/xml',
                'text/xml',
                'application/pdf',
                'text/plain'
            ];

            $fileExtension = strtolower($file->getClientOriginalExtension());
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            if (!in_array($fileExtension, $allowedExtensions) || !in_array($mimeType, $allowedMimeTypes)) {
                throw new \Exception('Invalid file type. Allowed types: ' . implode(', ', $allowedExtensions) . '.');
            }

            if ($fileSize > self::MAX_FILE_SIZE_BYTES) {
                throw new \Exception('File too large. Maximum size allowed is 5MiB.');
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
        $directory = "{$patientId}";
        Storage::disk('patient_records')->makeDirectory($directory);

        $fileName = $file->getClientOriginalName();
        return $file->storeAs($directory, $fileName, 'patient_records');
    }
}
