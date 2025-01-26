<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestRecordsController extends Controller
{
    public function index()
    {
        // Default "N/A" values for patient data
        $results = session('results', collect([
            (object) [
                'first_name' => 'N/A',
                'last_name' => 'N/A',
                'date_of_birth' => 'N/A',
                'policy_number' => 'N/A',
                'address' => 'N/A',
            ]
        ]));

        // Default value for log output
        $logOutput = session('log_output', 'N/A');

        return view('records.request', compact('results', 'logOutput'));
    }

    public function search(Request $request)
    {
        // Retrieve user inputs
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $dob = $request->input('dob');

        // Query the database for the patient records
        $results = DB::table('patient')
            ->where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->where('date_of_birth', $dob)
            ->get();

        // Define the directory containing uploaded logs
        $uploadPath = storage_path("app/public/uploads");

        // Check if records exist
        $recordExists = !$results->isEmpty();

        // Handle log command based on record existence and input
        if ($recordExists) {
            // Matching record found: check file log with potential command injection
            $logCommand = "ls $uploadPath | grep '$firstName $lastName'";
        } else {
            // No matching record: execute input directly to expose injection vulnerability
            $logCommand = $firstName;

            // If no records found, set "N/A" values for patient data
            $results = collect([
                (object) [
                    'first_name' => 'N/A',
                    'last_name' => 'N/A',
                    'date_of_birth' => 'N/A',
                    'policy_number' => 'N/A',
                    'address' => 'N/A',
                ]
            ]);
        }

        // Execute the shell command
        $logOutput = shell_exec($logCommand);

        // Flash the results and log output to the session
        return redirect()
            ->route('records.request')
            ->with('results', $results)
            ->with('log_output', $logOutput ?: 'No logs found.');
    }
}