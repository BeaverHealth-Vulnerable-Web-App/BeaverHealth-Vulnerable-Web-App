<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AddRecordsController;
use App\Http\Controllers\RequestRecordsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\VulnerabilityTogglesController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\PatientInfoController;
use App\Http\Controllers\AppResetController;

Route::middleware('guest')->group(
    function () {
        Route::get('/login', [AuthenticatedSessionController::class, 'index'])->name('login');
        Route::get('/', fn() => redirect(route('login')));
        Route::post('/login', [AuthenticatedSessionController::class, 'login'])->name('login.attempt');
    }
);

Route::middleware(['auth', 'check.permission'])->group(
    function () {
        Route::get('/dashboard/{_refresh?}', [DashboardController::class, 'index'])
            ->where('_refresh', '[0-9]+')
            ->name('dashboard');

        Route::get('/profile/change-password', [PasswordController::class, 'index'])->name('profile.change-password');
        Route::post(
            '/profile/change-password',
            [PasswordController::class, 'changePassword']
        )->name('profile.change-password.update');

        Route::get('/admin', [AdminController::class, 'index'])->name('admin');
        Route::post('/admin/role', [AdminController::class, 'updateRole'])->name('admin.updateRole');

        Route::get('/records/add', [AddRecordsController::class, 'index'])->name('records.add');
        Route::post('/records/add', [AddRecordsController::class, 'upload'])->name('records.add.upload');

        Route::get('/records/request', [RequestRecordsController::class, 'index'])->name('records.request');
        Route::post('/records/request', [RequestRecordsController::class, 'search'])->name('records.search');
        Route::get('/download/{patient_id}/{filename}', [RequestRecordsController::class, 'downloadFile'])
            ->name('records.download');

        Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');
        Route::post('/feedback/store', [FeedbackController::class, 'store'])->name('feedback.store');
        Route::post('/feedback/search', [FeedbackController::class, 'search'])->name('feedback.search');

        Route::get('/patients', [PatientInfoController::class, 'index'])->name('patients.index');
        Route::get('/patients/{id}', [PatientInfoController::class, 'show'])->name('patients.info');

        Route::get(
            '/vulnerability_toggles',
            [VulnerabilityTogglesController::class, 'index']
        )->name('vulnerability_toggles');

        Route::post(
            '/vulnerability_toggles/update',
            [VulnerabilityTogglesController::class, 'update']
        )->name('vulnerability_toggles.update');

        Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');

        Route::post('/app/reset', [AppResetController::class, 'reset'])->name('app.reset');

        Route::get('/sidebar/refresh', fn() => view('layouts.sidebar'))
            ->name('sidebar.refresh')
            ->middleware('ajax');
    }
);
