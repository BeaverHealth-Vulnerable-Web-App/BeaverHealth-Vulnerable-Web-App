<?php

namespace App\Services;

use App\Http\Requests\FeedbackCommentRequest;
use App\Http\Requests\FeedbackSearchRequest;
use App\Models\Patient;
use App\Models\PatientFeedback;
use App\Models\User;

class FeedbackService
{
    public function getFeedbackWithPatients()
    {
        $feedback = PatientFeedback::orderBy('created_at', 'desc')->get();
        $patients = Patient::all();
        return ['feedback' => $feedback, 'patients' => $patients];
    }

    public function storeFeedbackSecure(FeedbackCommentRequest $request)
    {
        $sanitizedFeedback = htmlspecialchars($request->input('feedback'));
        PatientFeedback::create([
            'patient_id' => $request->input('patient_id'),
            'feedback' => $sanitizedFeedback
        ]);
    }

    public function storeFeedbackInsecure(FeedbackCommentRequest $request)
    {
        PatientFeedback::create([
            'patient_id' => $request->input('patient_id'),
            'feedback' => $request->input('feedback')
        ]);
    }

    public function searchFeedbackSecure(FeedbackSearchRequest $request)
    {
        $name = htmlspecialchars($request->input('search_name'));
        $feedback = PatientFeedback::whereHas('patient', function ($query) use ($name) {
            $query->where('first_name', 'like', "%{$name}%")
                ->orWhere('last_name', 'like', "%{$name}%");
        })->orderBy('created_at', 'desc')->get();
        $patients = Patient::all();
        return [['feedback' => $feedback, 'patients' => $patients], $name];
    }

    public function searchFeedbackInsecure(FeedbackSearchRequest $request)
    {
        $name = $request->input('search_name');
        $feedback = PatientFeedback::whereHas('patient', function ($query) use ($name) {
            $query->where('first_name', 'like', "%{$name}%")
                ->orWhere('last_name', 'like', "%{$name}%");
        })->orderBy('created_at', 'desc')->get();
        $patients = Patient::all();
        return [['feedback' => $feedback, 'patients' => $patients], $name];
    }
}
