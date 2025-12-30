<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // PDF generation
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    /* ---------------- ADMIN DASHBOARD ---------------- */
    public function index()
    {
        $results = AssessmentResult::with('user')->orderByDesc('created_at')->get();
        $resumes = Resume::latest()->get()->keyBy('user_id');
        $totalUsers = \App\Models\User::count();
        $totalResumes = Resume::count();
        $usersWithAssessment = AssessmentResult::distinct('user_id')->count('user_id');

        return view('dashboard', compact(
            'results',
            'resumes',
            'totalUsers',
            'totalResumes',
            'usersWithAssessment'
        ));
    }

    public function showLoginForm()
    {
        return view('login');
    }

    /* ---------------- SHOW ASSESSMENT ---------------- */
    public function show()
    {
        $user = Auth::user();
        $TOTAL_TIME = 15 * 60; // 15 minutes in seconds

        // Check if user has a valid resume
        $resume = Resume::where('user_id', $user->id)->latest()->first();
        if (!$resume || $resume->status !== 'valid') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Please upload a valid resume before starting the assessment.');
        }

        // Fetch latest assessment result for user
        $result = AssessmentResult::where('user_id', $user->id)->latest()->first();

        // If user has already submitted the assessment, redirect
        if ($result && $result->is_submitted) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You have already completed the assessment.');
        }

        /* ----------------- CALCULATE REMAINING TIME ----------------- */
        if ($result && !$result->is_submitted) {
            // Use saved time_left from DB instead of started_at
            $timeLeft = $result->time_left ?? $TOTAL_TIME;
            $savedAnswers = $result->answers ?? [];

            // Load questions in the same order as assigned
            $questionIds = $result->question_ids ?? [];
            $questions = AssessmentQuestion::whereIn('id', $questionIds)
                ->orderByRaw("FIELD(id," . implode(',', $questionIds) . ")")
                ->get();
        } else {
            // New assessment
            $questions = AssessmentQuestion::inRandomOrder()->limit(20)->get();
            $result = AssessmentResult::create([
                'user_id' => $user->id,
                'question_ids' => $questions->pluck('id')->toArray(),
                'answers' => [],
                'time_left' => $TOTAL_TIME,
                'started_at' => now(),
                'is_submitted' => false,
            ]);
            $timeLeft = $TOTAL_TIME;
            $savedAnswers = [];
        }

        // Format questions for the view
        $questions = $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'question_type' => strtolower(trim($q->question_type)),
                'options' => is_string($q->options) ? json_decode($q->options, true) ?? [] : $q->options,
            ];
        });

        return view('assessment', compact('questions', 'timeLeft', 'savedAnswers'));
    }

    /* ---------------- AUTOSAVE ---------------- */
    public function autosave(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'answers' => 'nullable|array',
            'time_left' => 'required|integer|min:0',
        ]);

        $result = AssessmentResult::where('user_id', $user->id)->latest()->first();

        if ($result) {
            // Update saved answers and remaining time
            $result->update([
                'answers' => $data['answers'] ?? [],
                'time_left' => $data['time_left'],
            ]);
        }

        return response()->json(['saved' => true]);
    }

    /* ---------------- FINAL SUBMIT ---------------- */
    public function submit(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
    'answers' => 'required|array',
    'violations' => 'nullable|integer|min:0',
]);

        $result = AssessmentResult::where('user_id', $user->id)->latest()->first();
        if (!$result) {
            return redirect()->route('user.dashboard')->with('error', 'No assessment found.');
        }

        $questionIds = $result->question_ids ?? [];
        $questions = AssessmentQuestion::whereIn('id', $questionIds)->get()->keyBy('id');

        // Count correct answers
        $correct = 0;
        foreach ($questions as $q) {
            $qid = (string)$q->id;
            $ans = $data['answers'][$qid] ?? '';
            if (trim(strtolower($ans)) === trim(strtolower($q->correct_answer))) {
                $correct++;
            }
        }

        $result->update([
    'answers' => $data['answers'],
    'total_questions' => count($questions),
    'correct_answers' => $correct,
    'wrong_answers' => count($questions) - $correct,
    'violations' => $data['violations'] ?? 0,
    'is_submitted' => true,
    'time_left' => 0,
]);


        return redirect()->route('assessment.results');
    }

    /* ---------------- RESULTS ---------------- */
    public function results()
    {
        $user = Auth::user();

        $result = AssessmentResult::with('user')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$result) {
            return redirect()->route('user.dashboard')
                ->with('error', 'No assessment results found.');
        }

        $questionIds = $result->question_ids ?? [];
        $questions = AssessmentQuestion::whereIn('id', $questionIds)
            ->orderByRaw("FIELD(id," . implode(',', $questionIds) . ")")
            ->get()
            ->keyBy(fn($item) => (string)$item->id);

        return view('resultup', compact('result', 'questions'));
    }

    /* ---------------- USER DASHBOARD ---------------- */
    public function userDashboard()
    {
        $user = Auth::user();
        $resume = Resume::where('user_id', $user->id)->latest()->first();
        $resumeStatus = $resume ? $resume->status : null;

        $result = AssessmentResult::where('user_id', $user->id)->latest()->first();

        $assessmentStatus = 'Not Started';
        $inProgress = false;

        if ($result) {
            if ($result->is_submitted) {
                $assessmentStatus = 'Completed';
                $inProgress = false;
            } elseif ($result->started_at && !$result->is_submitted) {
                $assessmentStatus = 'In Progress';
                $inProgress = true;
            }
        }

        return view('user.dashboard', compact('resumeStatus', 'assessmentStatus', 'inProgress'));
    }

    /* ---------------- VIEW SPECIFIC RESULT ---------------- */
    public function viewResult($id)
    {
        $result = AssessmentResult::with('user')->find($id);
        if (!$result) {
            return redirect()->route('dashboard')->with('error', 'Result not found.');
        }

        $resume = Resume::where('user_id', $result->user_id)->latest()->first();
        $questionIds = $result->question_ids ?? [];
        $questions = AssessmentQuestion::whereIn('id', $questionIds)
            ->orderByRaw("FIELD(id," . implode(',', $questionIds) . ")")
            ->get()
            ->keyBy(fn($item) => (string)$item->id);

        return view('resultup', compact('result', 'resume', 'questions'));
    }

    /* ---------------- DOWNLOAD SINGLE PDF REPORT ---------------- */
    public function downloadReport($id)
    {
        $result = AssessmentResult::with('user')->findOrFail($id);
        $questionIds = $result->question_ids ?? [];
        $questions = AssessmentQuestion::whereIn('id', $questionIds)
            ->orderByRaw("FIELD(id," . implode(',', $questionIds) . ")")
            ->get()
            ->keyBy(fn($item) => (string)$item->id);

        $resume = Resume::where('user_id', $result->user_id)->latest()->first();
        $answers = $result->answers ?? [];

        $pdf = Pdf::loadView('pdf.report', compact('result', 'questions', 'resume', 'answers', 'questionIds'));
        $fileName = 'assessment_' . $result->user->id . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /* ---------------- BULK DOWNLOAD ALL REPORTS + RESUMES ---------------- */
    public function bulkDownload()
    {
        $results = AssessmentResult::with('user')->get();
        if ($results->isEmpty()) {
            return redirect()->back()->with('error', 'No assessment results found.');
        }

        $zipFileName = 'Assessment_download_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($results as $result) {
                $userFolder = $result->user->name ?: 'User_' . $result->user->id;
                $userFolder = preg_replace('/[^\w\-]/', '_', $userFolder);

                // Add Resume
                $resume = Resume::where('user_id', $result->user_id)->latest()->first();
                if ($resume && Storage::disk('public')->exists($resume->file_path)) {
                    $resumeContents = Storage::disk('public')->get($resume->file_path);
                    $resumeName = basename($resume->file_path);
                    $zip->addFromString("$userFolder/$resumeName", $resumeContents);
                }

                // Add PDF report
                $questionIds = $result->question_ids ?? [];
                $questions = AssessmentQuestion::whereIn('id', $questionIds)
                    ->orderByRaw("FIELD(id," . implode(',', $questionIds) . ")")
                    ->get()
                    ->keyBy(fn($item) => (string)$item->id);
                $answers = $result->answers ?? [];

                $pdf = Pdf::loadView('pdf.report', compact('result', 'questions', 'resume', 'answers', 'questionIds'));
                $zip->addFromString("$userFolder/assessment_report.pdf", $pdf->output());
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
