<?php

namespace App\Services;

use App\Http\Requests\FeedbackCommentRequest;
use App\Http\Requests\FeedbackSearchRequest;
use App\Models\Patient;
use App\Models\PatientFeedback;

class FeedbackService
{
    public function getFeedbackWithPatients()
    {
        $feedback = PatientFeedback::orderBy('created_at', 'desc')->get();
        $patients = Patient::all();
        return ['feedback' => $feedback, 'patients' => $patients];
    }

    public function storeFeedback(FeedbackCommentRequest $request)
    {
        PatientFeedback::create([
            'patient_id' => $request->input('patient_id'),
            'feedback' => $request->input('feedback')
        ]);
    }

    public function searchFeedback(FeedbackSearchRequest $request)
    {
        $name = $request->input('search_name');
        $feedback = PatientFeedback::whereHas('patient', function ($query) use ($name) {
            $query->where('first_name', 'like', "%{$name}%")
                ->orWhere('last_name', 'like', "%{$name}%");
        })->orderBy('created_at', 'desc')->get();
        $patients = Patient::all();
        return ['feedback' => $feedback, 'patients' => $patients];
    }
}
