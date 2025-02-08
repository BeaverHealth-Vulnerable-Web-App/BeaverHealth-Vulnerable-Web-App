<?php

namespace App\Services\Records;

use App\Services\Files\FileRetrievalService;
use App\Services\PatientService;
use App\Presenters\FilePresenter;

class RecordSearchService
{
    protected $patientService;
    protected $fileRetrievalService;
    protected $filePresenter;

    public function __construct(PatientService $patientService, FileRetrievalService $fileRetrievalService, FilePresenter $filePresenter)
    {
        $this->patientService = $patientService;
        $this->fileRetrievalService = $fileRetrievalService;
        $this->filePresenter = $filePresenter;
    }

    public function searchRecords($patientId, $keyword = '')
    {
        $patient = $this->patientService->findPatientById($patientId);
        if (!$patient) {
            return ['error' => 'Patient not found.'];
        }

        $recordsPath = $this->fileRetrievalService->getPatientRecordsPath($patient->patient_id);
        $fileList = $this->fileRetrievalService->listFiles($recordsPath, $keyword);
        $downloadLinks = $this->filePresenter->generateDownloadLinks($fileList, $patient->patient_id);

        return [
            'patientInfo' => $patient,
            'patientFiles' => count($downloadLinks) ? implode('<br>', $downloadLinks) : 'No files found.',
        ];
    }
}