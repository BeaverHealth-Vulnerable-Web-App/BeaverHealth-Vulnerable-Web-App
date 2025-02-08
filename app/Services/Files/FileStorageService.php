<?php

namespace App\Services\Files;

use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    public function storeFile($patientId, $file)
    {
        $directory = "patient_records/{$patientId}";
        Storage::makeDirectory($directory);

        $fileName = $file->getClientOriginalName();
        return $file->storeAs($directory, $fileName, 'public');
    }
}