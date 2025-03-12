<?php

namespace App\Services;

use App\Http\Requests\FeedbackCommentRequest;
use App\Http\Requests\FeedbackSearchRequest;
use App\Models\Patient;
use App\Models\PatientFeedback;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class FeedbackService
{
    private function processInput(string $userInput, bool $useRaw): string
    {
        return $useRaw ? $userInput : e($userInput);
    }

    private function sanitizeStoredFeedback(Collection &$feedback): void
    {
        foreach ($feedback as $item) {
            if ($item->is_vulnerable) {
                $item->feedback = e($item->feedback);
            }
        }
    }

    public function getFeedbackWithPatients()
    {
        $feedback = PatientFeedback::orderBy('created_at', 'desc')->get();
        if (!auth()->user()->xss_stored_on) {
            $this->sanitizeStoredFeedback($feedback);
        }
        return ['feedback' => $feedback, 'patients' => Patient::all()];
    }

    public function storeFeedback(FeedbackCommentRequest $request)
    {
        $user = auth()->user();
        $patientId = $request->input('patient_id');
        $feedback = $request->input('feedback');
        $xssStoredToggle = $user->xss_stored_on;

        Log::channel('user_activity')->info('User stored patient feedback', [
            'username' => $user->username,
            'patient_id' => $patientId,
            'feedback' => $feedback,
            'xss_enabled' => $xssStoredToggle ? true : false,
        ]);

        PatientFeedback::create([
            'patient_id' => $patientId,
            'feedback' => $this->processInput($feedback, $xssStoredToggle),
            'is_vulnerable' => $xssStoredToggle ? true : false
        ]);
    }

    public function searchFeedback(FeedbackSearchRequest $request)
    {
        $user = auth()->user();
        $name = $this->processInput($request->input('search_name'), $user->xss_reflected_on);

        Log::channel('user_activity')->info('User searched for patient feedback', [
            'username' => $user->username,
            'search_term' => $name,
            'xss_enabled' => $user->xss_reflected_on,
        ]);

        $feedback = PatientFeedback::whereHas('patient', function ($query) use ($name) {
            $query->where('first_name', 'like', "%{$name}%")
                ->orWhere('last_name', 'like', "%{$name}%");
        })->orderBy('created_at', 'desc')->get();
        $this->sanitizeStoredFeedback($feedback); // Sanitized so that it doesn't interfere with the Reflective XSS
        return [['feedback' => $feedback, 'patients' => Patient::all()], $name];
    }
}
