<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AddRecordsController;
use App\Http\Controllers\RequestRecordsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\VulnerabilityTogglesController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(
    function () {
        Route::get('/', [AuthenticatedSessionController::class, 'showLoginPage']);
        Route::get('login', [AuthenticatedSessionController::class, 'showLoginPage'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'handleLoginRequest']);

        Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
        Route::post('register', [RegisteredUserController::class, 'store']);

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    }
);

Route::middleware('auth')->group(
    function () {
        Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/admin', [AdminController::class, 'index'])->name('admin');
        Route::post('/admin/update-role', [AdminController::class, 'updateRole'])->name('admin.updateRole');

        Route::get('/records/add', [AddRecordsController::class, 'index'])->name('records.add');
        Route::get('/records/request', [RequestRecordsController::class, 'index'])->name('records.request');

        Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');
        Route::post('/feedback/store', [FeedbackController::class, 'store'])->name('feedback.store');
        Route::get('/feedback/search', [FeedbackController::class, 'search'])->name('feedback.search');

        Route::get('/vulnerability_toggles', [VulnerabilityTogglesController::class, 'index'])->name('toggles');

        Route::get('confirm-password', [AuthenticatedSessionController::class, 'confirm'])->name('password.confirm');
        Route::put('password', [PasswordController::class, 'update'])->name('password.update');
        Route::post('logout', [AuthenticatedSessionController::class, 'handleLogoutRequest'])->name('logout');
    }
);

