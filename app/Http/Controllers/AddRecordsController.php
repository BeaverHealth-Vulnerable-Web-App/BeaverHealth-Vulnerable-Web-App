<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Patient;
use App\Models\PatientFile;

class AddRecordsController extends Controller
{
    public function index()
    {
        return view('records.add');
    }

    public function upload(Request $request)
    {   
        $request->validate([
            'patient_first_name' => 'required|string',
            'patient_last_name'  => 'required|string',
            'patient_dob'        => 'required|date',
            'medical_record'     => 'required|file|max:10240',
        ]);

        $patientFirstName = $request->input('patient_first_name');
        $patientLastName  = $request->input('patient_last_name');
        $patientDOB       = $request->input('patient_dob');

        $patientInfo = Patient::where('first_name', $patientFirstName)
                              ->where('last_name', $patientLastName)
                              ->where('date_of_birth', $patientDOB)
                              ->first();

        if (!$patientInfo) {
            return back()->with('error', 'Patient not found.');
        }

        $sanitizedPatientName = preg_replace('/[^a-zA-Z0-9]/', '_', "{$patientInfo->first_name}_{$patientInfo->last_name}");
        $patientRecordsPath   = "patient_records/{$patientInfo->patient_id}_{$sanitizedPatientName}";

        Storage::makeDirectory($patientRecordsPath);

        $uploadedFile = $request->file('medical_record');
        $fileName     = $uploadedFile->getClientOriginalName();
        $filePath     = $uploadedFile->storeAs($patientRecordsPath, $fileName, 'public');

        PatientFile::create([
            'patient_id' => $patientInfo->patient_id,
            'filename'   => $fileName,
            'path'       => $filePath
        ]);

        return back()->with('success', 'File uploaded successfully!');
    }
}