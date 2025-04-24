<?php

namespace App\Services;

use App\Http\Requests\FeedbackCommentRequest;
use App\Http\Requests\FeedbackSearchRequest;
use App\Models\Patient;
use App\Models\PatientFeedback;
use App\Services\UserActivityLogger;
use Illuminate\Database\Eloquent\Collection;

class FeedbackService
{
    public function __construct(private UserActivityLogger $logger)
    {
    }

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
        $xssStoredOn = auth()->user()->xss_stored_on;
        $patientId = $request->input('patient_id');
        $rawFeedback = $request->input('feedback');
        $processedFeedback = $this->processInput($rawFeedback, $xssStoredOn);

        $this->logger->info('Patient feedback stored', [
            'patient_id'         => $patientId,
            'raw_feedback'       => $rawFeedback,
            'processed_feedback' => $processedFeedback,
            'xss_stored_on'      => $xssStoredOn
        ]);

        PatientFeedback::create([
            'patient_id' => $patientId,
            'feedback' => $processedFeedback,
            'is_vulnerable' => $xssStoredOn
        ]);
    }

    public function searchFeedback(FeedbackSearchRequest $request)
    {
        $xssReflectedOn = auth()->user()->xss_reflected_on;
        $rawSearchTerm = $request->input('search_name');
        $processedSearchTerm = $this->processInput($rawSearchTerm, $xssReflectedOn);

        $this->logger->info('Patient feedback searched', [
            'raw_search_term'       => $rawSearchTerm,
            'processed_search_term' => $processedSearchTerm,
            'xss_reflected_on'      => $xssReflectedOn
        ]);

        $feedback = PatientFeedback::whereHas('patient', function ($query) use ($processedSearchTerm) {
            $query->where('first_name', 'like', "%{$processedSearchTerm}%")
                ->orWhere('last_name', 'like', "%{$processedSearchTerm}%");
        })->orderBy('created_at', 'desc')->get();
        $this->sanitizeStoredFeedback($feedback); // Sanitized so that it doesn't interfere with the Reflective XSS
        return [['feedback' => $feedback, 'patients' => Patient::all()], $processedSearchTerm];
    }
}
