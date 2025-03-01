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
        $file = $request->file('medical_record');
        $filename = $file->getClientOriginalName();

        $this->patientRecordService->storeRecord(
            $request->input('patient_id'),
            $file
        );

        return back()->with('success', "File '{$filename}' uploaded successfully!");
    }
}
