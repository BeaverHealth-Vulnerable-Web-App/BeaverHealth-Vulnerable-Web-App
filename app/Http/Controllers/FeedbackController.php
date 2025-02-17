<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackCommentRequest;
use App\Http\Requests\FeedbackSearchRequest;
use App\Models\Patient;
use App\Models\PatientFeedback;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = PatientFeedback::orderBy('created_at', 'desc')->get();
        $patients = Patient::all();
        return view('feedback.index', compact('feedback', 'patients'));
    }

    public function store(FeedbackCommentRequest $request)
    {
        PatientFeedback::create([
            'patient_id' => $request->input('patient_id'),
            'feedback' => $request->input('feedback')
        ]);

        return redirect()->route('feedback')
            ->with('success', 'Feedback added successfully!');
    }

    public function search(FeedbackSearchRequest $request)
    {
        $name = $request->input('search_name');
        $feedback = PatientFeedback::whereHas('patient', function ($query) use ($name) {
            $query->where('first_name', 'like', "%{$name}%")
                ->orWhere('last_name', 'like', "%{$name}%");
        })->orderBy('created_at', 'desc')->get();
        $patients = Patient::all();

        return view('feedback.index', compact('feedback', 'patients'))->with('search_name', $name);
    }
}
