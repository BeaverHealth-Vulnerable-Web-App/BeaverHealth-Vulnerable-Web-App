<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestRecordsController extends Controller
{
    public function index()
    {
        $patientInfo = session('patient_info', collect([
            (object) [
                'first_name'     => 'N/A',
                'last_name'      => 'N/A',
                'date_of_birth'  => 'N/A',
            ]
        ]));

        $patientFiles = session('patient_files', 'No files found.');

        return view('records.request', compact('patientInfo', 'patientFiles'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'dob'        => 'required|date',
        ]);

        $patientFirstName = $request->input('first_name');
        $patientLastName  = $request->input('last_name');
        $patientDOB       = $request->input('dob');
        $searchKeyword    = trim($request->input('keyword', ''));

        $patientInfo = DB::table('patient')
            ->where('first_name', $patientFirstName)
            ->where('last_name', $patientLastName)
            ->where('date_of_birth', $patientDOB)
            ->first();

        if (!$patientInfo) {
            return redirect()
                ->route('records.request')
                ->with('error', 'Patient not found.');
        }

        $sanitizedPatientName = preg_replace('/[^a-zA-Z0-9]/', '_', "{$patientInfo->first_name}_{$patientInfo->last_name}");
        $patientRecordsPath   = storage_path("app/public/patient_records/{$patientInfo->patient_id}_{$sanitizedPatientName}");

        if (!is_dir($patientRecordsPath)) {
            return redirect()
                ->route('records.request')
                ->with('error', 'No files found. (Directory missing)');
        }

        $fileSearchCommand = empty($searchKeyword)
            ? "ls -1 {$patientRecordsPath} 2>&1"
            : "ls -1 {$patientRecordsPath} | grep -i {$searchKeyword} 2>&1";

        $fileList = array_filter(explode("\n", shell_exec($fileSearchCommand) ?? ''), 'strlen');

        $downloadLinks = [];
        foreach ($fileList as $file) {
            $downloadUrl = route('records.download', [
                'patient_id'     => $patientInfo->patient_id,
                'sanitized_name' => $sanitizedPatientName,
                'filename'       => $file
            ]);
            $downloadLinks[] = "<a href='{$downloadUrl}' class='text-blue-500 underline'>{$file}</a>";
        }

        $patientFiles = count($downloadLinks)
            ? implode('<br>', $downloadLinks)
            : 'No files found.';

        return redirect()
            ->route('records.request')
            ->with('patient_info', collect([$patientInfo]))
            ->with('patient_files', $patientFiles);
    }

    public function downloadFile($patient_id, $sanitized_name, $filename)
    {
        $filePath = storage_path("app/public/patient_records/{$patient_id}_{$sanitized_name}/{$filename}");

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath);
    }
}