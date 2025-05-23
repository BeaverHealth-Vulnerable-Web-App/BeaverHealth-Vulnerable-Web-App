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
        $patients = Patient::select('patient_id', 'first_name', 'last_name', 'ssn')->get();
        return view('records.add', compact('patients'));
    }

    public function upload(StoreRecordRequest $request)
    {
        try {
            $file = $request->file('medical_record');
            $filename = $file->getClientOriginalName();

            $this->patientRecordService->storeRecord(
                $request->input('patient_id'),
                $file
            );

            session()->flash('records-status', [
                'type' => 'success',
                'message' => "File '{$filename}' uploaded successfully!"
            ]);
        } catch (\Exception $e) {
            session()->flash('records-status', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        return back();
    }
}
