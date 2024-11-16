<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/', function () {
        return view('auth.login');
    }
);

Route::get(
    '/dashboard', function () {
        return view('dashboard');
    }
)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(
    function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    }
);

Route::get('/admin', [AdminController::class, 'index'])->name('admin')->middleware('auth');
Route::post('/admin/update-role', [AdminController::class, 'updateRole'])->name('admin.updateRole');

// Update to point to new route once built
Route::get(
    '/records_request', function () {
        return view('dashboard');
    }
)->middleware(['auth', 'verified'])->name('records_request');

// Update to point to new route once built
Route::get(
    '/records_add', function () {
        return view('dashboard');
    }
)->middleware(['auth', 'verified'])->name('records_add');

// Update to point to new route once built
Route::get(
    '/feedback', [CommentController::class, 'index']
)->middleware(['auth', 'verified'])->name('feedback');
Route::post(
    '/feedback/store', [CommentController::class, 'store']
)->middleware(['auth', 'verified'])->name('feedback.store');
Route::get(
    '/feedback/search', [CommentController::class, 'search']
)->middleware(['auth', 'verified'])->name('feedback.search');

// Update to point to new route once built
Route::get(
    '/vulnerability_toggles', function () {
        return view('dashboard');
    }
)->middleware(['auth', 'verified'])->name('vulnerability_toggles');

require __DIR__.'/auth.php';
