<?php

namespace App\Http\Controllers;

use App\Services\UserActivityLogger;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AppResetController extends Controller
{
    /**
     * Displays the reset confirmation view.
     *
     * @return View
     */
    public function confirmReset(): View
    {
        return view('components.confirm-reset');
    }

    /**
     * Resets the application. Deletes patient files, wipes and resets
     * the database, and logs out the current user.
     *
     * @param UserActivityLogger $logger The user activity logger
     * @return RedirectResponse
     */
    public function reset(UserActivityLogger $logger): RedirectResponse
    {
        $logger->info('User reset application');

        $this->deletePatientRecordFiles();
        Artisan::call('db:wipe');
        Artisan::call('migrate');
        Artisan::call('db:seed');
        Auth::logout();

        return redirect()->route('login')
            ->with('status', 'Application has been reset successfully.');
    }

    /**
     * Deletes all patient record files.
     *
     * @return void
     */
    private function deletePatientRecordFiles(): void
    {
        $publicPath = storage_path('app/public/patient_records');
        $privatePath = storage_path('app/private/patient_records');

        if (File::isDirectory($publicPath)) {
            File::deleteDirectory($publicPath);
            File::makeDirectory($publicPath);
        }
        if (File::isDirectory($privatePath)) {
            File::deleteDirectory($privatePath);
            File::makeDirectory($privatePath);
        }
    }
}
