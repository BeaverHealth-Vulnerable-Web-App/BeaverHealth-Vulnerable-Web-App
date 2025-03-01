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
        $this->feedbackService->storeFeedback($request);
        return redirect()->route('feedback')
            ->with('success', 'Feedback added successfully!');
    }

    public function search(FeedbackSearchRequest $request)
    {
        $searchResults = $this->feedbackService->searchFeedback($request);
        if (auth()->user()->xss_reflected_on) {
            $search_name = $request->input('search_name');
            return view('feedback.index', $searchResults)->with('search_name', $search_name);
        }
        $sanitized_name = htmlspecialchars($request->input('search_name'));
        return view('feedback.index', $searchResults)->with('search_name', $sanitized_name);
    }
}
