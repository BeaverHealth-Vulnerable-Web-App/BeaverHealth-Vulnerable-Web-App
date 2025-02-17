<?php

namespace App\Http\Controllers;

use App\Http\Requests\Records\StoreRecordRequest;
use App\Services\PatientRecordService;
use App\Models\Patient;

class AddRecordsController extends Controller
{
    protected $patientRecordService;

    public function __construct(PatientRecordService $patientRecordService)
    {
        $this->patientRecordService = $patientRecordService;
    }

    public function index()
    {
        $patients = Patient::all();
        return view('records.add', compact('patients'));
    }

    public function upload(StoreRecordRequest $request)
    {
        $this->patientRecordService->storeRecord(
            $request->input('patient_id'),
            $request->file('medical_record')
        );

        return back()->with('success', 'File uploaded successfully!');
    }
}
