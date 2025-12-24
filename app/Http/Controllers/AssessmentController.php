<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function index()
{
    // Only admin should access this
    $results = AssessmentResult::with('user')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('dashboard', compact('results'));
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
        ->get()
        ->map(function ($q) {
            // Decode JSON options into an array
            $options = $q->options;
            if (is_string($options)) {
                $options = json_decode($options, true) ?? [];
            }

            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'question_type' => strtolower(trim($q->question_type)),
                'options' => is_array($options) ? $options : [],
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

        $questions = AssessmentQuestion::whereIn(
            'id',
            array_keys($data['answers'])
        )->get()->keyBy('id');

        $correct = 0;
        $wrong = 0;

        foreach ($data['answers'] as $qid => $answer) {
            if (isset($questions[$qid])) {
                $questions[$qid]->correct_answer == $answer
                    ? $correct++
                    : $wrong++;
            }
        }

        AssessmentResult::updateOrCreate(
            ['user_id' => $user->id],
            [
                'answers' => $data['answers'],
                'total_questions' => count($data['answers']),
                'correct_answers' => $correct,
                'wrong_answers' => $wrong,
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

}
