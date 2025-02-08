<?php

namespace App\Services;

use App\Models\Patient;

class PatientService
{
    public function findPatientById($patientId)
    {
        return Patient::find($patientId);
    }
}