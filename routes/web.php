<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AddRecordsController;
use App\Http\Controllers\RequestRecordsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\VulnerabilityTogglesController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;


// Login
Route::get('/', [AuthenticatedSessionController::class, 'create']);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile
Route::middleware('auth')->group(
    function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    }
);

// Admin
Route::middleware('auth')->group(
    function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin');
        Route::post('/admin/update-role', [AdminController::class, 'updateRole'])->name('admin.updateRole');
    }
);

// Add Records
Route::get('/records_add', [AddRecordsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('records_add');

// Request Records
Route::get('/records_request', [RequestRecordsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('records_request');

// Patient Feedback
Route::get('/feedback', [FeedbackController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('feedback');
Route::post('/feedback/store', [FeedbackController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('feedback.store');
Route::get('/feedback/search', [FeedbackController::class, 'search'])
    ->middleware(['auth', 'verified'])
    ->name('feedback.search');

// Vulnerability Toggles
Route::get('/vulnerability_toggles', [VulnerabilityTogglesController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('vulnerability_toggles');
Route::post('/vulnerability_toggles/store', [VulnerabilityTogglesController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('vulnerability_toggles.store');

require __DIR__ . '/auth.php';
