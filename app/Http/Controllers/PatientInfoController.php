<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\PatientInfoService;
use App\Models\Patient;

class PatientInfoController extends Controller
{
    /**
     * Creates a new PatientInfoController instance.
     *
     * @param PatientInfoService $patientInfoService The service for handling patient information.
     */
    public function __construct(private PatientInfoService $patientInfoService)
    {
    }

    /**
     * Display the patient search page and results.
     *
     * @param  Request  $request The incoming request.
     * @return View|RedirectResponse A view with search results or a redirect response.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $searchTerm      = (string) $request->input('search', '');
        $patients        = [];
        $searchPerformed = false;

        if ($searchTerm !== '') {
            $searchPerformed = true;

            try {
                $patients = $this->patientInfoService
                                 ->searchPatients($searchTerm, Auth::user()->sqli_on);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['search' => 'Invalid search query. Please adjust your input.'])
                    ->withInput();
            }
        }

        return view('patients.index', compact('patients', 'searchPerformed'));
    }

    /**
     * Display detailed information about a specific patient.
     *
     * @param  int $id The ID of the patient to display.
     * @return View|RedirectResponse A view with patient information or a redirect response.
     */
    public function show($id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return redirect()->route('patients.index')->with('error', 'Patient not found.');
        }

        return view('patients.details', compact('patient'));
    }
}
