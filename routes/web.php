<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\ResumeUploadController;
use App\Http\Controllers\AdminInviteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Homepage
Route::get('/', fn () => view('welcome'));

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AssessmentController::class, 'showLoginForm'])->name('login');
    Route::get('/login/microsoft', [MicrosoftController::class, 'redirectToProvider'])->name('login.microsoft');
    Route::get('/login/microsoft/callback', [MicrosoftController::class, 'handleProviderCallback']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {

    // ---------- Admin ----------
    Route::get('/dashboard', [AssessmentController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:admin');

    // Admin invite (single email)
    Route::post('/admin/invite/single', [AdminInviteController::class, 'inviteSingle'])
        ->name('admin.invite.single')
        ->middleware('role:admin');

    // Admin invite (CSV upload)
    Route::post('/admin/invite/csv', [AdminInviteController::class, 'inviteCsv'])
        ->name('admin.invite.csv')
        ->middleware('role:admin');

    // ---------- User ----------
    Route::get('/resumeup', [ResumeUploadController::class, 'create'])
        ->name('resume.upload.form')
        ->middleware('role:user');

    Route::post('/resumeup', [ResumeUploadController::class, 'store'])
        ->name('resume.upload.store')
        ->middleware('role:user');

    Route::get('/user/dashboard', [AssessmentController::class, 'userDashboard'])
    ->name('user.dashboard')
    ->middleware('role:user');


    // ---------- Assessment ----------
    Route::get('/assessment', [AssessmentController::class, 'show'])
        ->name('assessment.start');

    Route::post('/assessment/autosave', [AssessmentController::class, 'autosave'])
        ->name('assessment.autosave');

    Route::post('/assessment/submit', [AssessmentController::class, 'submit'])
        ->name('assessment.submit');

    // User results (own result)
    Route::get('/assessment/results', [AssessmentController::class, 'results'])
        ->name('assessment.results');

    // Admin view specific result
    Route::get('/assessment/results/{id}', [AssessmentController::class, 'viewResult'])
        ->name('assessment.results.view')
        ->middleware('role:admin');

    // Admin download report
    Route::get('/assessment/{id}/download', [AssessmentController::class, 'downloadReport'])
        ->name('assessment.download')
        ->middleware('role:admin');

    // Admin download resume
    Route::get('/resume/{id}/download', [ResumeUploadController::class, 'download'])
    ->name('resume.download')
    ->middleware('role:admin');

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
