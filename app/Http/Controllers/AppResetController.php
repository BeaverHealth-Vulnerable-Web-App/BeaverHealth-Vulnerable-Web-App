<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class AppResetController extends Controller
{
    public function reset()
    {
        $this->deletePatientRecordFiles();

        Artisan::call('db:wipe');
        Artisan::call('migrate');
        Artisan::call('db:seed');

        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Application has been reset successfully.');
    }

    private function deletePatientRecordFiles()
    {
        $publicPath = storage_path('app/public/patient_records');
        if (is_dir($publicPath)) {
            $files = glob($publicPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        $privatePath = storage_path('app/private/patient_records');
        if (is_dir($privatePath)) {
            $files = glob($privatePath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
