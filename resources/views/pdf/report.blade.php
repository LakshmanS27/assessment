<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Assessment Report</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            padding: 30px; 
            font-size: 12px;
            line-height: 1.5;
        }
        .logo { width: 150px; }
        h1, h2 { text-align: center; margin-bottom: 20px; }
        h2 { margin-top: 50px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px 10px; border: 1px solid #000; text-align: left; }
        th { background-color: #f2f2f2; }
        .page-break { page-break-before: always; }
        .question { margin-bottom: 10px; }
        .answer { margin-left: 20px; margin-bottom: 5px; }
        .correct { color: green; font-weight: bold; }
        .incorrect { color: red; font-weight: bold; }
        hr { border: 0; border-top: 1px solid #ccc; margin: 10px 0; }
    </style>
</head>
<body>

<!-- ==================== SUMMARY PAGE ==================== -->
<div style="text-align: center;">
    <img src="{{ public_path('images/logo_sq.png') }}" class="logo">
    <h1>Assessment Report</h1>
</div>

<table>
    <tr>
        <td><strong>Name</strong></td>
        <td>{{ $result->user->name }}</td>
    </tr>
    <tr>
        <td><strong>Email</strong></td>
        <td>{{ $result->user->email }}</td>
    </tr>
    <tr>
        <td><strong>Resume Status</strong></td>
        <td>{{ $resume ? ucfirst($resume->status) : 'No Resume' }}</td>
    </tr>
    <tr>
        <td><strong>Correct Answers</strong></td>
        <td>{{ $result->correct_answers }}</td>
    </tr>
    <tr>
        <td><strong>Wrong Answers</strong></td>
        <td>{{ $result->wrong_answers }}</td>
    </tr>
    
    <tr>
    <td><strong>Wrong Answers</strong></td>
    <td>{{ $result->wrong_answers }}</td>
</tr>
<tr>
    <td><strong>Violations</strong></td>
    <td>
        {{ $result->violations }}
        @if($result->violations >= 3)
            (Auto-submitted)
        @endif
    </td>
</tr>
<tr>
    <td><strong>Total Score</strong></td>
    <td>{{ $result->correct_answers }} / {{ $result->total_questions }}</td>
</tr>
</table>

<!-- ==================== DETAILED ANSWERS ==================== -->
<div class="page-break"></div>
<h2>Detailed Assessment Answers</h2>

@php
    $answers = $answers ?? [];
@endphp

@foreach($questionIds = $result->question_ids ?? [] as $index => $qid)
    @php
        $question = $questions[$qid] ?? null;
        $userAnswer = $answers[$qid] ?? 'No Answer';
        $correctAnswer = $question->correct_answer ?? 'N/A';
        $isCorrect = trim(strtolower($userAnswer)) === trim(strtolower($correctAnswer));
    @endphp

    <div class="question">
        <strong>Question {{ $index + 1 }}:</strong> {{ $question->question_text ?? 'N/A' }}
    </div>
    <div class="answer">
        <strong>Your answer:</strong> <span class="{{ $isCorrect ? 'correct' : 'incorrect' }}">{{ $userAnswer }}</span>
    </div>
    <div class="answer">
        <strong>Correct answer:</strong> {{ $correctAnswer }}
    </div>
    <hr>
@endforeach

</body>
</html>
