<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackCommentRequest;
use App\Http\Requests\FeedbackSearchRequest;
use App\Services\FeedbackService;
use App\Models\Patient;

class FeedbackController extends Controller
{
    protected FeedbackService $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    public function index()
    {
        $data = $this->feedbackService->getFeedbackWithPatients();
        $data['patients'] = Patient::select('patient_id', 'first_name', 'last_name', 'ssn')->get();
        return view('feedback.index', $data);
    }

    public function store(FeedbackCommentRequest $request)
    {
        $this->feedbackService->storeFeedback($request);
        session()->flash('feedback-status', [
            'type' => 'success',
            'message' => 'Feedback added successfully!'
        ]);
        return redirect()->route('feedback');
    }

    public function search(FeedbackSearchRequest $request)
    {
        $searchResults = $this->feedbackService->searchFeedback($request);
        return view('feedback.index', $searchResults[0])->with('search_name', $searchResults[1]);
    }
}
