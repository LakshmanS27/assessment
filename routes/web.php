<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\ResumeUploadController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Homepage
Route::get('/', fn() => view('welcome'));

// Guest routes (not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AssessmentController::class, 'showLoginForm'])->name('login');
    Route::get('login/microsoft', [MicrosoftController::class, 'redirectToProvider'])->name('login.microsoft');
    Route::get('login/microsoft/callback', [MicrosoftController::class, 'handleProviderCallback']);
});

// Authenticated routes (logged in)
Route::middleware('auth')->group(function () {

    // Dashboard (admin only)
    Route::get('/dashboard', [AssessmentController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:admin');

    // Resume upload (user only)
    Route::get('/resumeup', [ResumeUploadController::class, 'create'])
        ->name('resume.upload.form')
        ->middleware('role:user');

    Route::post('/resumeup', [ResumeUploadController::class, 'store'])
        ->name('resume.upload.store')
        ->middleware('role:user');

    // Logout (works for both admin dashboard & user resume upload)
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
