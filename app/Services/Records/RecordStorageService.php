<?php

namespace App\Services\Records;

use App\Services\Files\FileStorageService;
use App\Services\PatientService;
use App\Models\PatientFile;

class RecordStorageService
{
    protected $patientService;
    protected $fileStorageService;

    public function __construct(PatientService $patientService, FileStorageService $fileStorageService)
    {
        $this->patientService = $patientService;
        $this->fileStorageService = $fileStorageService;
    }

    public function storeRecord($patientId, $file)
    {
        $patient = $this->patientService->findPatientById($patientId);
        if (!$patient) {
            throw new \Exception('Patient not found.');
        }

        $filePath = $this->fileStorageService->storeFile($patient->patient_id, $file);

        return PatientFile::create([
            'patient_id' => $patient->patient_id,
            'filename'   => $file->getClientOriginalName(),
            'path'       => $filePath
        ]);
    }
}