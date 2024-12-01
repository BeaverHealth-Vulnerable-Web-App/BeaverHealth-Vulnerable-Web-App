<?php

namespace App\Http\Controllers;

use App\Models\PatientFeedback;
use App\Models\Patient;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display the feedback index page with feedback for all patients.
     */
    public function index()
    {
        $feedback = PatientFeedback::with('patient')->orderBy('created_at', 'desc')->get();
        return view('feedback.index', compact('feedback'));
    }

    /**
     * Store a new feedback entry. Creates a patient if patient does not exist.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            ]
        );

        // Attempt to find an existing patient
        $patient = Patient::where('first_name', $request->first_name)
                          ->where('last_name', $request->last_name)
                          ->first();

        // If patient doesn't exist, create a new one
        if (!$patient) {
            $patient = Patient::create(
                [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                ]
            );
        }

        // Create the feedback associated with the patient
        PatientFeedback::create(
            [
            'patient_id' => $patient->patient_id,
            'feedback'   => $request->feedback, // Not sanitized
            ]
        );

        return redirect()->route('feedback')
            ->with('success', 'Feedback added successfully!');
    }

    /**
     * Search feedback by patient's first or last name.
     */
    public function search(Request $request)
    {
        $name = $request->input('search_name');

        $feedback = PatientFeedback::whereHas(
            'patient', function ($query) use ($name) {
                            $query->where('first_name', 'like', "%{$name}%")
                                ->orWhere('last_name', 'like', "%{$name}%");
            }
        )
                        ->with('patient')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('feedback.index', compact('feedback'))->with('search_name', $name);
    }
}
