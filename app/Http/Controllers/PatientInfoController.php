<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\PatientInfoService;
use Illuminate\Support\Facades\Auth;

class PatientInfoController extends Controller
{
    public function __construct(private PatientInfoService $patientInfoService)
    {
    }

    /**
     * Display the patient search page and results.
     *
     * @param  Request  $request
     * @return View|RedirectResponse
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
}
