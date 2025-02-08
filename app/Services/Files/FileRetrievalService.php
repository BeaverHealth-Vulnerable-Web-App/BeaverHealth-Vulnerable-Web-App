<?php

namespace App\Services\Files;

use Illuminate\Support\Facades\Storage;

class FileRetrievalService
{
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
}