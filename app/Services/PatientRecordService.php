<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Patient;
use App\Models\PatientFile;
use App\Presenters\FilePresenter;
use App\Services\UserActivityLogger;

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

    public function getPatientRecordsPath($patientId)
    {
        return storage_path("app/patient_records/{$patientId}");
    }

    public function searchRecords($patientId, $keyword = '')
    {
        $user = auth()->user();

        $patient = Patient::find($patientId);
        if (!$patient) {
            return ['error' => 'Patient not found.'];
        }

        try {
            $recordsPath = $this->getPatientRecordsPath($patientId);
            $fileList = $this->listFiles($recordsPath, $keyword, $user->cmd_inject_on);
            $downloadLinks = $this->filePresenter->generateDownloadLinks($fileList, $patientId);

            $this->logSearchAttempt($patientId, $keyword, $user->cmd_inject_on, null);

            return [
                'patientInfo' => $patient,
                'patientFiles' => count($downloadLinks) ? implode('<br>', $downloadLinks) : 'No files found.',
            ];
        } catch (\Exception $e) {
            $this->logSearchAttempt($patientId, $keyword, $user->cmd_inject_on, $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function storeRecord($patientId, $file)
    {
        $user = auth()->user();

        $patient = Patient::find($patientId);
        if (!$patient) {
            $error = 'Patient not found.';
            $this->logger->info('File upload attempt', ['error' => $error]);
            throw new \Exception($error);
        }

        $filename = $file->getClientOriginalName();
        $fileExtension = strtolower($file->getClientOriginalExtension());
        $fileSize = $file->getSize();
        $fileMimeType = $file->getMimeType();

        if (!$user->file_upload_vuln_on) {
            $error = $this->validateFile($fileExtension, $fileMimeType, $fileSize);
            $this->logUploadAttempt(
                $patientId,
                $filename,
                $fileExtension,
                $fileMimeType,
                $fileSize,
                $user->file_upload_vuln_on,
                $error
            );
            if ($error !== null) {
                throw new \Exception($error);
            }
        }

        $filePath = $this->storeFile($patientId, $file);

        $this->logUploadAttempt(
            $patientId,
            $filename,
            $fileExtension,
            $fileMimeType,
            $fileSize,
            $user->file_upload_vuln_on,
            null
        );

        return PatientFile::create([
            'patient_id' => $patientId,
            'filename'   => $filename,
            'path'       => $filePath
        ]);
    }

    private function listFiles($directory, $keyword = '', $isInsecure = false)
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

    private function validateFile($extension, $mimeType, $size): ?string
    {
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return 'Invalid file extension. Allowed extensions: ' . implode(', ', self::ALLOWED_EXTENSIONS) . '.';
        }
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return 'Invalid file type. Allowed MIME types: ' . implode(', ', self::ALLOWED_MIME_TYPES) . '.';
        }
        if ($size > self::MAX_FILE_BYTES) {
            return "File too large. Maximum size allowed is 5MiB.";
        }
        return null;
    }

    private function storeFile($patientId, $file)
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
        $this->logger->info('User searched for patient files', [
            'patient_id'    => $patientId,
            'search_term'   => $searchTerm,
            'cmd_inject_on' => $cmdInjectOn,
            'success'       => $error === null,
            'error'         => $error
        ]);
    }

    private function logUploadAttempt(
        int $patientId,
        string $filename,
        string $fileExtension,
        string $fileMimeType,
        int $fileSize,
        bool $insecureFileUploadOn,
        ?string $error
    ) {
        $this->logger->info('File upload attempt', [
            'patient_id'              => $patientId,
            'filename'                => $filename,
            'file_extension'          => $fileExtension,
            'file_mime_type'          => $fileMimeType,
            'file_size'               => $fileSize,
            'file_upload_vuln_on'     => $insecureFileUploadOn,
            'success'                 => $error === null,
            'error'                   => $error
        ]);
    }
}
