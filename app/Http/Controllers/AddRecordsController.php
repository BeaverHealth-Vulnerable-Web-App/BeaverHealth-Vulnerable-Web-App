<?php

namespace App\Http\Controllers;

use App\Http\Requests\Records\StoreRecordRequest;
use App\Services\Records\RecordStorageService;
use App\Models\Patient;

class AddRecordsController extends Controller
{
    protected $recordStorageService;

    public function __construct(RecordStorageService $recordStorageService)
    {
        $this->recordStorageService = $recordStorageService;
    }

    public function index()
    {
        $patients = Patient::all();
        return view('records.add', compact('patients'));
    }

    public function upload(StoreRecordRequest $request)
    {
        $this->recordStorageService->storeRecord(
            $request->input('patient_id'),
            $request->file('medical_record')
        );

        return back()->with('success', 'File uploaded successfully!');
    }
}
