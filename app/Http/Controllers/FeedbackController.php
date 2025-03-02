<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackCommentRequest;
use App\Http\Requests\FeedbackSearchRequest;
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
        return view(
            'feedback.index',
            $this->feedbackService->getFeedbackWithPatients(
                auth()->user()->xss_stored_on
            )
        );
    }

    public function store(FeedbackCommentRequest $request)
    {
        $this->feedbackService->storeFeedback(
            $request,
            auth()->user()->xss_stored_on
        );
        return redirect()->route('feedback')
            ->with('success', 'Feedback added successfully!');
    }

    public function search(FeedbackSearchRequest $request)
    {
        $searchResults = $this->feedbackService->searchFeedback(
            $request,
            auth()->user()->xss_reflected_on
        );
        return view('feedback.index', $searchResults[0])
            ->with('search_name', $searchResults[1]);
    }
}
