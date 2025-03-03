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

        $secureMode = !auth()->user()->insecure_mode_on;

        try {
            $this->patientRecordService->storeRecord(
                $request->input('patient_id'),
                $file,
                $secureMode
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
