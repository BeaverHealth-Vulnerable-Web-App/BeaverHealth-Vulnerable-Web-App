<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\PatientInfoService;
use Illuminate\Support\Facades\Auth;

class PatientInfoController extends Controller
{
    /** @var PatientInfoService */
    protected PatientInfoService $patientInfoService;

    /**
     * PatientInfoController constructor.
     *
     * @param  PatientInfoService  $patientInfoService
     */
    public function __construct(PatientInfoService $patientInfoService)
    {
        $this->patientInfoService = $patientInfoService;
    }

    /**
     * Display the patient search page and results.
     *
     * @param  Request  $request
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        $term            = $request->input('search', '');
        $patients        = [];
        $searchPerformed = false;
        $user            = Auth::user();

        if ($term !== '') {
            $searchPerformed = true;

            try {
                $patients = $this->patientInfoService
                                 ->searchPatients($term, $user->sqli_on);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['search' => 'Invalid search query. Please adjust your input.'])
                    ->withInput();
            }
        }

        return view('patients.index', compact('patients', 'searchPerformed'));
    }
}
