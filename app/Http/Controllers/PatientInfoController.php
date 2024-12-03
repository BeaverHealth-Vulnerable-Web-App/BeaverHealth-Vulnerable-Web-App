<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientInfoController extends Controller
{
    /**
     * Show the list of all patients.
     */
    public function index()
    {
        $patients = Patient::all(); // Fetch all patients
        return view('patients.index', compact('patients'));
    }

    /**
     * Show details for a single patient.
     */
    public function show($id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return redirect()->route('patients.index')->with('error', 'Patient not found.');
        }

        return view('patients.info', compact('patient'));
    }
}
