<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\Patient;
use App\Models\PatientFile;
use App\Presenters\FilePresenter;

class PatientRecordService
{
    private const MAX_FILE_BYTES = 5 * 1024 * 1024;

    private const ALLOWED_EXTENSIONS = [
        'csv', 'xlsx', 'json', 'edi', 'xml', 'pdf', 'txt'
    ];

    private const ALLOWED_MIME_TYPES = [
        'text/csv',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/json',
        'application/EDI-X12',
        'application/xml',
        'text/xml',
        'application/pdf',
        'text/plain'
    ];

    public function __construct(
        private FilePresenter $filePresenter,
        private UserActivityLogger $logger
    ) {
    }

    public function getPatientRecordsPath(int $patientId): string
    {
        return storage_path("app/patient_records/{$patientId}");
    }

    public function searchRecords(int $patientId, string $keyword = ''): array
    {
        $cmdInjectOn = auth()->user()->cmd_inject_on;

        $patient = Patient::find($patientId);
        if (!$patient) {
            $error = 'Patient does not exist';
            $this->logSearchAttempt($patientId, $keyword, $cmdInjectOn, $error);
            return ['error' => $error];
        }

        try {
            $recordsPath = $this->getPatientRecordsPath($patientId);
            $fileList = $this->listFiles($recordsPath, $keyword, $cmdInjectOn);
            $downloadLinks = $this->filePresenter->generateDownloadLinks($fileList, $patientId);

            $this->logSearchAttempt($patientId, $keyword, $cmdInjectOn, null);

            return [
                'patientInfo' => $patient,
                'patientFiles' => count($downloadLinks) ? implode('<br>', $downloadLinks) : 'No files found.',
            ];
        } catch (\Exception $e) {
            $this->logSearchAttempt($patientId, $keyword, $cmdInjectOn, $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function storeRecord(int $patientId, UploadedFile $file)
    {
        $fileUploadVulnOn = auth()->user()->file_upload_on;

        $fileName = $file->getClientOriginalName();
        $fileExtension = strtolower($file->getClientOriginalExtension());
        $fileSize = $file->getSize();
        $fileMimeType = $file->getMimeType();

        $error = null;

        if (!Patient::where('patient_id', $patientId)->exists()) {
            $error = 'Patient does not exist';
        } elseif (!$fileUploadVulnOn) {
            $error = $this->validateFile($fileExtension, $fileMimeType, $fileSize);
        }

        $this->logger->info('Patient file upload attempt', [
            'patient_id'      => $patientId,
            'filename'        => $fileName,
            'file_extension'  => $fileExtension,
            'file_size'       => $fileSize,
            'file_mime_type'  => $fileMimeType,
            'file_upload_on'  => $fileUploadVulnOn,
            'success'         => $error === null,
            'error'           => $error
        ]);

        if ($error !== null) {
            throw new \Exception($error);
        }

        $filePath = $this->storeFile($patientId, $file);

        return PatientFile::create([
            'patient_id' => $patientId,
            'filename'   => $fileName,
            'path'       => $filePath
        ]);
    }

    private function listFiles(string $directory, string $keyword, string $cmdInjectOn)
    {
        if (!is_dir($directory) && empty($keyword)) {
            return [];
        }

        if ($cmdInjectOn) {
            $command = empty($keyword)
                ? "ls -1 {$directory} 2>&1"
                : "ls -1 {$directory} | grep -i {$keyword} 2>&1";

            return array_filter(explode("\n", shell_exec($command) ?? ''), 'strlen');
        }

        if (preg_match('/[;|&$><`\]}\.\/]/', $keyword)) {
            throw new \Exception('Invalid characters detected in search query.');
        }

        $files = array_diff(scandir($directory), ['.', '..']);
        if (!$files) {
            return [];
        }

        $safeKeyword = preg_replace('/[^a-zA-Z0-9_-]/', '', $keyword);

        return array_filter($files, function ($file) use ($safeKeyword) {
            return stripos($file, $safeKeyword) !== false;
        });
    }

    private function validateFile(string $extension, ?string $mimeType, int|false $size): ?string
    {
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return 'Invalid file extension. Allowed extensions: ' . implode(', ', self::ALLOWED_EXTENSIONS) . '.';
        }
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return 'Invalid MIME type';
        }
        if ($size !== null && $size > self::MAX_FILE_BYTES) {
            return "File too large. Maximum size allowed is 5MiB.";
        }
        return null;
    }

    private function storeFile(int $patientId, UploadedFile $file)
    {
        $directory = "{$patientId}";
        Storage::disk('patient_records')->makeDirectory($directory);

        $fileName = $file->getClientOriginalName();
        return $file->storeAs($directory, $fileName, 'patient_records');
    }

    private function logSearchAttempt(
        int $patientId,
        string $searchTerm,
        bool $cmdInjectOn,
        ?string $error
    ) {
        $this->logger->info('Patient file search attempt', [
            'patient_id'    => $patientId,
            'search_term'   => $searchTerm,
            'cmd_inject_on' => $cmdInjectOn,
            'success'       => $error === null,
            'error'         => $error
        ]);
    }
}
