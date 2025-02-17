<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackCommentRequest;
use App\Http\Requests\FeedbackSearchRequest;
use App\Models\Patient;
use App\Models\PatientFeedback;
use App\Services\FeedbackService;

class FeedbackController extends Controller
{
    protected FeedbackService $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    public function index()
    {
        $feedback = PatientFeedback::orderBy('created_at', 'desc')->get();
        $patients = Patient::all();
        return view('feedback.index', compact('feedback', 'patients'));
    }

    public function store(FeedbackCommentRequest $request)
    {
        $this->feedbackService->storeFeedback($request);
        return redirect()->route('feedback')
            ->with('success', 'Feedback added successfully!');
    }

    public function search(FeedbackSearchRequest $request)
    {
        $searchResults = $this->feedbackService->searchFeedback($request);
        return view('feedback.index', $searchResults)->with('search_name', $request->input('search_name'));
    }
}
