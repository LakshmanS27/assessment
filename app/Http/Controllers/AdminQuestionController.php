<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminQuestionController extends Controller
{
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = fgetcsv($file);

        $expected = ['question_text','question_type','options','correct_answer'];

        if (array_map('strtolower', $header) !== $expected) {
            return back()->with('error', 'Invalid CSV format.');
        }

        $inserted = 0;

        while (($row = fgetcsv($file)) !== false) {
            [$text, $type, $options, $answer] = $row;

            $type = strtolower(trim($type));

            if (!in_array($type, ['text','multiple_choice'])) {
                continue;
            }

            AssessmentQuestion::create([
                'question_text' => trim($text),
                'question_type' => $type,
                'options' => $type === 'multiple_choice'
                    ? array_map('trim', explode(',', $options))
                    : null,
                'correct_answer' => trim($answer),
            ]);

            $inserted++;
        }

        fclose($file);

        return back()->with('success', "$inserted questions uploaded successfully.");
    }
}
