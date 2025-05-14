<?php

namespace App\Http\Controllers;

use App\Http\Requests\Records\PatientSearchRequest;
use App\Services\PatientRecordService;

class RequestRecordsController extends Controller
{
    protected $patientRecordService;

    public function __construct(PatientRecordService $patientRecordService)
    {
        $this->patientRecordService = $patientRecordService;
    }

    public function index()
    {
        $patients = \App\Models\Patient::select('patient_id', 'first_name', 'last_name', 'ssn')->get();
        $patientInfo = session('patient_info', collect([
            (object) ['first_name' => 'N/A', 'last_name' => 'N/A', 'date_of_birth' => 'N/A']
        ]));
        $patientFiles = session('patient_files', 'No files found.');

        return view('records.request', compact('patients', 'patientInfo', 'patientFiles'));
    }

    public function search(PatientSearchRequest $request)
    {
        $searchResults = $this->patientRecordService->searchRecords(
            $request->input('patient_id'),
            $request->input('keyword')
        );

        if (isset($searchResults['error'])) {
            return redirect()->route('records.request')
                             ->with('records-request-status', [
                                 'message' => $searchResults['error'],
                                 'type' => 'error'
                             ]);
        }

        return redirect()->route('records.request')
                         ->with('patient_info', collect([$searchResults['patientInfo']]))
                         ->with('patient_files', $searchResults['patientFiles']);
    }

    public function downloadFile($patient_id, $filename)
    {
        $filePath = $this->patientRecordService->getPatientRecordsPath($patient_id) . "/{$filename}";

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath);
    }
}
