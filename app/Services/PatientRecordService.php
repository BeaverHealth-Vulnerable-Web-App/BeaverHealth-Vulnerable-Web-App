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

    public function listFiles($directory, $keyword = '')
    {
        if (!is_dir($directory)) {
            return [];
        }

        $command = empty($keyword)
            ? "ls -1 {$directory} 2>&1"
            : "ls -1 {$directory} | grep -i {$keyword} 2>&1";

        return array_filter(explode("\n", shell_exec($command) ?? ''), 'strlen');
    }

    public function storeFile($patientId, $file)
    {
        $directory = "patient_records/{$patientId}";
        Storage::makeDirectory($directory);

        $fileName = $file->getClientOriginalName();
        return $file->storeAs($directory, $fileName, 'public');
    }

    public function searchRecords($patientId, $keyword = '')
    {
        $patient = Patient::find($patientId);
        if (!$patient) {
            return ['error' => 'Patient not found.'];
        }

        $recordsPath = $this->getPatientRecordsPath($patient->patient_id);
        $fileList = $this->listFiles($recordsPath, $keyword);
        $downloadLinks = $this->filePresenter->generateDownloadLinks($fileList, $patient->patient_id);

        return [
            'patientInfo' => $patient,
            'patientFiles' => count($downloadLinks) ? implode('<br>', $downloadLinks) : 'No files found.',
        ];
    }

    public function storeRecord($patientId, $file, $secureMode = true)
    {
        $patient = Patient::find($patientId);
        if (!$patient) {
            throw new \Exception('Patient not found.');
        }
        if ($secureMode) {
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes

            $extension = strtolower($file->getClientOriginalExtension());
            $fileSize = $file->getSize();

            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception('Invalid file type. Only PDF, JPG, and PNG are allowed.');
            }

            if ($fileSize > $maxFileSize) {
                throw new \Exception('File is too large. Maximum size allowed is 5MB.');
            }

            $filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
            $directory = "patient_records/{$patient->patient_id}";
            Storage::makeDirectory($directory);
            $filePath = $file->storeAs($directory, $filename, 'public');
        } else {
            $directory = "patient_records/{$patient->patient_id}";
            Storage::makeDirectory($directory);
            $filePath = $file->storeAs($directory, $file->getClientOriginalName(), 'public');
        }

        return PatientFile::create([
            'patient_id' => $patient->patient_id,
            'filename'   => $file->getClientOriginalName(),
            'path'       => $filePath
        ]);
    }
}
