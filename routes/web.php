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


// Login page
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

// Records
Route::middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/records_add', [AddRecordsController::class, 'index'])->name('records_add');
        Route::get('/records_request', [RequestRecordsController::class, 'index'])->name('records_request');
    }
);

// Feedback
Route::get('/feedback', [FeedbackController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('feedback');

// Vulnerability Toggles
Route::get('/vulnerability_toggles', [VulnerabilityTogglesController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('vulnerability_toggles');

require __DIR__ . '/auth.php';
