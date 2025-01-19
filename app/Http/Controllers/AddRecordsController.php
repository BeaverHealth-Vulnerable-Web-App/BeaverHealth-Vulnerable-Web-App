<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddRecordsController extends Controller
{
    public function index()
    {
        return view('records.add-records');
    }

    // File upload
    public function upload(Request $request)
    {   
        // Allow only files (no other validation for now)
        $request->validate([
            'file' => 'required|file',
        ]);

        // Retrieve the uploaded file
        $file = $request->file('file');

        // Get filename
        $filename = $file->getClientOriginalName();

        // Store the file in storage/app/public/uploads
        $path = $file->storeAs('uploads', $filename, 'public');

        return back()->with('success', 'File uploaded successfully!');
    }
}

