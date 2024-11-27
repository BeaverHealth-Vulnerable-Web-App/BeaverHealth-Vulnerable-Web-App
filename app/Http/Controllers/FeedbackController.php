<?php

namespace App\Http\Controllers;

use App\Models\PatientFeedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = PatientFeedback::orderBy('created_at', 'desc')->get();
        return view('feedback.index', compact('feedback'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'feedback' => 'required'
        ]);

        $validated['name'] = htmlspecialchars($validated['name']);

        PatientFeedback::create($validated);

        return redirect()->route('feedback')
            ->with('success', 'Feedback added successfully!');
    }

    public function search(Request $request)
    {
        $name = $request->input('search_name');
        $feedback = PatientFeedback::where('name', 'like', "%{$name}%")->get();

        return view('feedback.index', compact('feedback'))->with('search_name', $name);
    }
}
