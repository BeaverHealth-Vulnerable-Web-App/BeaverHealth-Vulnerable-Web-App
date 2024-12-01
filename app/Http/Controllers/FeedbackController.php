<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientFeedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = PatientFeedback::orderBy('created_at', 'desc')->get();
        return view('feedback.index', compact('feedback'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|max:255',
            'lname' => 'required|max:255',
            'feedback' => 'required'
        ]);

        $validated['fname'] = htmlspecialchars($validated['fname']);
        $validated['lname'] = htmlspecialchars($validated['lname']);

        $patient = Patient::where('first_name', $validated['fname'])
            ->where('last_name', $validated['lname'])
            ->first();

        if (!$patient) {
            $patient = Patient::create([
                'first_name' => $validated['fname'],
                'last_name' => $validated['lname']
            ]);
        }

        PatientFeedback::create([
            'patient_id' => $patient->patient_id,
            'feedback' => $validated['feedback']
        ]);

        return redirect()->route('feedback')
            ->with('success', 'Feedback added successfully!');
    }

    public function search(Request $request)
    {
        $name = $request->input('search_name');
        $feedback = PatientFeedback::whereHas('patient', function ($query) use ($name) {
            $query->where('first_name', 'like', "%{$name}%")
                ->orWhere('last_name', 'like', "%{$name}%");
        })->orderBy('created_at', 'desc')->get();

        return view('feedback.index', compact('feedback'))->with('search_name', $name);
    }
}
