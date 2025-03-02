<?php

namespace App\Services;

use App\Http\Requests\FeedbackCommentRequest;
use App\Http\Requests\FeedbackSearchRequest;
use App\Models\Patient;
use App\Models\PatientFeedback;

class FeedbackService
{
    public function getFeedbackWithPatients(bool $xss_stored_on)
    {
        $feedback = PatientFeedback::orderBy('created_at', 'desc')->get();
        if ($xss_stored_on) {
            $feedback->each(function ($item) {
                $item->feedback = e($item->feedback);
            });
        }

        return ['feedback' => $feedback, 'patients' => Patient::all()];
    }

    public function storeFeedback(FeedbackCommentRequest $request, bool $xss_stored_on)
    {
        $feedback = $request->input('feedback');
        if (!$xss_stored_on) {
            $feedback = htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8');
            $feedback = e($feedback);
        }

        PatientFeedback::create([
            'patient_id' => $request->input('patient_id'),
            'feedback' => $feedback
        ]);
    }

    public function searchFeedback(FeedbackSearchRequest $request, bool $xss_reflected_on)
    {
        $name = $request->input('search_name');
        if (!$xss_reflected_on) {
            $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        }

        $feedback = PatientFeedback::whereHas('patient', function ($query) use ($name) {
            $query->where('first_name', 'like', "%{name}%")
            ->orWhere('last_name', 'like', "%{name}%");
        })->orderBy('created_at', 'desc')->get();

        return [['feedback' => $feedback, 'patients' => Patient::all()], $name];
    }
}
