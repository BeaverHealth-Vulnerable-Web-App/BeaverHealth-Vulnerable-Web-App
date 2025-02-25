<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AddRecordsController;
use App\Http\Controllers\RequestRecordsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\VulnerabilityTogglesController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PatientInfoController;

Route::middleware('guest')->group(
    function () {
        Route::get('/login', [AuthenticatedSessionController::class, 'index'])->name('login');
        Route::get('/', fn() => redirect(route('login')));
        Route::post('/login', [AuthenticatedSessionController::class, 'login'])->name('login.attempt');

        Route::get('/register', [RegisteredUserController::class, 'index'])->name('register');
        Route::post('/register', [RegisteredUserController::class, 'register'])->name('register.attempt');
    }
);

Route::middleware('auth')->group(
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

        Route::get('/sidebar/refresh', function () {
            return view('layouts.sidebar');
        })->name('sidebar.refresh');
    }
);
