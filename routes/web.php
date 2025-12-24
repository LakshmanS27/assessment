<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\ResumeUploadController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Homepage
Route::get('/', fn () => view('welcome'));

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AssessmentController::class, 'showLoginForm'])->name('login');
    Route::get('login/microsoft', [MicrosoftController::class, 'redirectToProvider'])->name('login.microsoft');
    Route::get('login/microsoft/callback', [MicrosoftController::class, 'handleProviderCallback']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [AssessmentController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:admin');

    Route::get('/resumeup', [ResumeUploadController::class, 'create'])
        ->name('resume.upload.form')
        ->middleware('role:user');

    Route::post('/resumeup', [ResumeUploadController::class, 'store'])
        ->name('resume.upload.store')
        ->middleware('role:user');

    // Assessment
    Route::get('/assessment', [AssessmentController::class, 'show'])
        ->name('assessment.start');

    // Autosave (NEW)
    Route::post('/assessment/autosave', [AssessmentController::class, 'autosave'])
        ->name('assessment.autosave');

    // Final submit
    Route::post('/assessment/submit', [AssessmentController::class, 'submit'])
        ->name('assessment.submit');

    // Results
    Route::get('/assessment/results', [AssessmentController::class, 'results'])
        ->name('assessment.results');
    
    Route::get('/assessment/results/{id}', [AssessmentController::class, 'viewResult'])
    ->name('assessment.results')
    ->middleware('role:admin');

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
