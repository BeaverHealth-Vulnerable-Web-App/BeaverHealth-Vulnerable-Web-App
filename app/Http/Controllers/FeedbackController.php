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
        $data = $this->feedbackService->getFeedbackWithPatients();
        return view('feedback.index', $data);
    }

    public function store(FeedbackCommentRequest $request)
    {
        if (auth()->user()->xss_stored_on) {
            $this->feedbackService->storeFeedbackInsecure($request);
        } else {
            $this->feedbackService->storeFeedbackSecure($request);
        }
        return redirect()->route('feedback')
            ->with('success', 'Feedback added successfully!');
    }

    public function search(FeedbackSearchRequest $request)
    {
        if (auth()->user()->xss_reflected_on) {
            $searchResults = $this->feedbackService->searchFeedbackInsecure($request);
            return view('feedback.index', $searchResults[0])->with('search_name', $searchResults[1]);
        }
        $searchResults = $this->feedbackService->searchFeedbackSecure($request);
        return view('feedback.index', $searchResults[0])->with('search_name', $searchResults[1]);
    }
}
