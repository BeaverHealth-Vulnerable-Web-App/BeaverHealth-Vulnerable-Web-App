<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::orderBy('created_at', 'desc')->get();
        return view('feedback.index', compact('comments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'comment' => 'required'
        ]);

        Comment::create($validated);

        return redirect()->route('feedback.index')
            ->with('success', 'Comment added successfully!');
    }

    public function search(Request $request)
    {
        $name = $request->input('search_name');
        $comments = Comment::where('name', 'like', "%{$name}%")->get();

        return view('feedback.index', compact('comments'))->with('search_name', $name);
    }
}
