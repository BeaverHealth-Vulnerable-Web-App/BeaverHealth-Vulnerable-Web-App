<?php

namespace App\Services;

use App\Http\Requests\FeedbackCommentRequest;
use App\Http\Requests\FeedbackSearchRequest;
use App\Models\Patient;
use App\Models\PatientFeedback;
use Illuminate\Database\Eloquent\Collection;

class FeedbackService
{
    private function processString(string $string, bool $toggle): string
    {
        if ($toggle) {
            return $string;
        } else {
            return e($string);
        }
    }

    private function sanitizeStoredFeedback(Collection &$feedback): void
    {
        for ($i = 0; $i < count($feedback); $i++) {
            $feedback[$i]->feedback = html_entity_decode($feedback[$i]->feedback);
            $feedback[$i]->feedback = e($feedback[$i]->feedback);
        }
    }

    public function getFeedbackWithPatients()
    {
        $feedback = PatientFeedback::orderBy('created_at', 'desc')->get();
        $patients = Patient::all();
        if (auth()->user()->xss_stored_on) {
            return ['feedback' => $feedback, 'patients' => $patients];
        }
        $this->sanitizeStoredFeedback($feedback);
        return ['feedback' => $feedback, 'patients' => $patients];
    }

    public function storeFeedback(FeedbackCommentRequest $request)
    {
        $feedback = $this->processString($request->input('feedback'), auth()->user()->xss_stored_on);

        PatientFeedback::create([
            'patient_id' => $request->input('patient_id'),
            'feedback' => $feedback
        ]);
    }

    public function searchFeedback(FeedbackSearchRequest $request)
    {
        $name = $this->processString($request->input('search_name'), auth()->user()->xss_reflected_on);

        $feedback = PatientFeedback::whereHas('patient', function ($query) use ($name) {
            $query->where('first_name', 'like', "%{$name}%")
                ->orWhere('last_name', 'like', "%{$name}%");
        })->orderBy('created_at', 'desc')->get();
        $patients = Patient::all();
        $this->sanitizeStoredFeedback($feedback);
        return [['feedback' => $feedback, 'patients' => $patients], $name];
    }
}
