<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Services\PatientInfoService;
use Illuminate\Support\Facades\Auth;

class PatientInfoController extends Controller
{
    protected $patientInfoService;

    public function __construct(PatientInfoService $patientInfoService)
    {
        $this->patientInfoService = $patientInfoService;
    }

    public function index(Request $request)
    {
        $searchTerm = $request->input('search', '');
        $patients = [];
        $searchPerformed = false;
        $user = Auth::user();

        try {
            if (!empty($searchTerm) && $user->sqli_on) {
                $patients = $this->patientInfoService->searchPatients($searchTerm);
                $searchPerformed = true;
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['search' => 'Invalid search query. Please adjust your input.'])
                ->withInput();
        }

        return view('patients.index', compact('patients', 'searchPerformed'));
    }

    public function show($id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return redirect()->route('patients.index')->withErrors(['patient' => 'Patient not found.']);
        }

        return view('patients.info', compact('patient'));
    }
}
