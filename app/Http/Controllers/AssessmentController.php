<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use Illuminate\Http\Request;
use App\Models\Resume;
use Illuminate\Support\Facades\Auth;
use PDF;


class AssessmentController extends Controller
{
   public function index()
{
    // Only admin should access
    $results = AssessmentResult::with('user')
        ->orderBy('created_at', 'desc')
        ->get();

    // Get latest resume per user
    $resumes = \App\Models\Resume::latest('created_at')
        ->get()
        ->keyBy('user_id');

    // Stats
    $totalUsers = \App\Models\User::count();
    $totalResumes = \App\Models\Resume::count();
    $usersWithAssessment = \App\Models\AssessmentResult::distinct('user_id')->count('user_id');

    return view('dashboard', compact('results', 'resumes', 'totalUsers', 'totalResumes', 'usersWithAssessment'));
}

    public function showLoginForm()
    {
        return view('login');
    }


    // Show assessment
    public function show()
{
    $questions = AssessmentQuestion::inRandomOrder()
        ->limit(20)
        ->get();

    // Save IDs in session for reference on submit
    session(['assessment_question_ids' => $questions->pluck('id')->toArray()]);

    // Transform questions for frontend
    $questions = $questions->map(function ($q) {
        $options = is_string($q->options) ? json_decode($q->options, true) ?? [] : $q->options;
        return [
            'id' => $q->id,
            'question_text' => $q->question_text,
            'question_type' => strtolower(trim($q->question_type)),
            'options' => $options,
        ];
    });

    return view('assessment', compact('questions'));
}



    // ✅ AUTOSAVE (SAFE)
    public function autosave(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'answers' => 'nullable|array'
        ]);

        AssessmentResult::updateOrCreate(
            ['user_id' => $user->id],
            [
                'answers' => $data['answers'] ?? [],
            ]
        );

        return response()->json(['saved' => true]);
    }

    // ✅ FINAL SUBMIT (REDIRECTS)
    public function submit(Request $request)
{
    $user = Auth::user();

    $data = $request->validate([
        'answers' => 'required|array'
    ]);

    // Use original 20-question IDs
    $questionIds = session('assessment_question_ids', []); // Get all 20 question IDs assigned
    $allQuestions = AssessmentQuestion::whereIn('id', $questionIds)->get()->keyBy('id');

    $correct = 0;

    foreach ($allQuestions as $q) {
        $submittedAnswer = $data['answers'][$q->id] ?? '';
        if (trim($submittedAnswer) == trim($q->correct_answer)) {
            $correct++;
    }
    }

    AssessmentResult::updateOrCreate(
        ['user_id' => $user->id],
        [
            'answers' => $data['answers'],
            'question_ids' => $questionIds, // store assigned questions
            'total_questions' => count($allQuestions),
            'correct_answers' => $correct,
            'wrong_answers' => count($allQuestions) - $correct,
        ]
    );


    return redirect()->route('assessment.results');
}



    // Results page
    public function results()
{
    $user = Auth::user();

    $result = AssessmentResult::with('user')
        ->where('user_id', $user->id)
        ->latest()
        ->first();

    if ($result && $result->answers) {
        $questions = AssessmentQuestion::whereIn('id', array_keys($result->answers))
            ->get()
            ->keyBy('id');
    } else {
        $questions = collect();
    }

    return view('resultup', compact('result', 'questions'));
}

public function viewResult($id)
{
    $result = AssessmentResult::with('user')->findOrFail($id);

    $questions = collect();
    if ($result->answers) {
        $questions = AssessmentQuestion::whereIn('id', array_keys($result->answers))
            ->get()
            ->keyBy('id');
    }

    return view('resultup', compact('result', 'questions'));
}

public function downloadReport($id)
    {
        // Fetch the assessment result with user
        $result = AssessmentResult::with('user')->findOrFail($id);

        // Fetch latest resume for the user
        $resume = Resume::where('user_id', $result->user_id)
            ->latest('created_at')
            ->first();

        // Fetch assigned questions in order
        $questionIds = $result->question_ids ?? [];
        $questions = AssessmentQuestion::whereIn('id', $questionIds)
            ->orderByRaw("FIELD(id," . implode(',', $questionIds) . ")")
            ->get()
            ->keyBy('id'); // key by ID for easy lookup

        // Decode user answers (JSON)
        $answers = $result->answers ?? [];

        // Pass all data to PDF view
        $pdf = PDF::loadView('pdf.report', [
            'result' => $result,
            'resume' => $resume,
            'questions' => $questions,
            'answers' => $answers,
        ])->setPaper('A4', 'portrait');

        $filename = 'Assessment_Report_' . $result->user->name . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

public function userDashboard()
{
    $user = Auth::user();

    // Check if resume uploaded
    $resume = Resume::where('user_id', $user->id)->latest()->first();
    $resumeStatus = $resume ? $resume->status : null;

    // Check if assessment started / in progress
    $result = AssessmentResult::where('user_id', $user->id)->latest()->first();
    $assessmentStatus = null;
    $inProgress = false;

    if ($result) {
        if ($result->answers && count($result->answers) < ($result->total_questions ?? 20)) {
            $assessmentStatus = 'In Progress';
            $inProgress = true;
        } elseif ($result->answers && count($result->answers) == ($result->total_questions ?? 20)) {
            $assessmentStatus = 'Completed';
        }
    } else {
        $assessmentStatus = 'Not Started';
    }

    // Correct Blade view path
    return view('user.dashboard', compact('resumeStatus', 'assessmentStatus', 'inProgress'));
}
  
}
