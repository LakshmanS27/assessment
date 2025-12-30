<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\ResumeUploadController;
use App\Http\Controllers\AdminInviteController;
use App\Http\Controllers\AdminQuestionController; 
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where all the web routes for the application are registered.
| Routes are grouped by middleware (guest, auth) and role (admin, user).
|
*/

// -------------------- Public / Guest Routes --------------------
// Route::get('/', fn () => view('welcome')); // Homepage

// 2. Set your custom home page as the root URL

Route::get('/', function () {
    return view('welcome');
})->name('home');

// This is still needed for your login button to work
Route::get('/login', function () {
    return view('auth.login'); 
})->name('login');

Route::middleware('guest')->group(function () {
    // Login form
    Route::get('/login', [AssessmentController::class, 'showLoginForm'])->name('login');

    // Microsoft OAuth login
    Route::get('/login/microsoft', [MicrosoftController::class, 'redirectToProvider'])->name('login.microsoft');
    Route::get('/login/microsoft/callback', [MicrosoftController::class, 'handleProviderCallback']);
});

// -------------------- Authenticated Routes --------------------
Route::middleware('auth')->group(function () {

    // -------------------- Admin Routes --------------------
    Route::middleware('role:admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AssessmentController::class, 'index'])->name('dashboard');

        // Invite Users
        Route::post('/admin/invite/single', [AdminInviteController::class, 'inviteSingle'])->name('admin.invite.single');
        Route::post('/admin/invite/csv', [AdminInviteController::class, 'inviteCsv'])->name('admin.invite.csv');

        // Assessment Results for any user (admin)
        Route::get('/assessment/results/{id}', [AssessmentController::class, 'viewResult'])->name('assessment.results.view');

        // Download assessment report (PDF)
        Route::get('/assessment/{id}/download', [AssessmentController::class, 'downloadReport'])->name('assessment.download');

        // Download user resume
        Route::get('/resume/{id}/download', [ResumeUploadController::class, 'download'])->name('resume.download');

        // Bulk download all assessment reports + resumes
        Route::get('/assessment/download-all', [AssessmentController::class, 'bulkDownload'])->name('assessment.bulk.download');

        Route::middleware(['auth','role:admin'])->group(function () {
        Route::post('/admin/questions/upload-csv',
        [AdminQuestionController::class, 'uploadCsv']
            )->name('admin.questions.upload');
        });
    });

    // -------------------- User Routes --------------------
    Route::middleware('role:user')->group(function () {

        // Resume upload
        Route::get('/resumeup', [ResumeUploadController::class, 'create'])->name('resume.upload.form');
        Route::post('/resumeup', [ResumeUploadController::class, 'store'])->name('resume.upload.store');

        // User Dashboard
        Route::get('/user/dashboard', [AssessmentController::class, 'userDashboard'])->name('user.dashboard');

        // Assessment routes
        Route::get('/assessment', [AssessmentController::class, 'show'])->name('assessment.start');
        Route::post('/assessment/autosave', [AssessmentController::class, 'autosave'])->name('assessment.autosave');
        Route::post('/assessment/submit', [AssessmentController::class, 'submit'])->name('assessment.submit');

        // View assessment results for logged-in user
        Route::get('/assessment/results', [AssessmentController::class, 'results'])->name('assessment.results');
    });

    // -------------------- Logout Route --------------------
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
