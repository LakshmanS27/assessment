<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-lg border-0">
                <div class="card-header text-center bg-primary text-white">
                    <h3 class="mb-0">Your Assessment Results</h3>
                </div>

                <div class="card-body">

                    @if($result)
                        <p><strong>Name:</strong> {{ $result->user->name }}</p>
                        <p><strong>Email:</strong> {{ $result->user->email }}</p>
                        <p><strong>Date:</strong> {{ $result->created_at->format('d M Y') }}</p>

                        <p class="fw-bold">
                            Score: {{ $result->correct_answers }} / {{ $result->total_questions }}
                        </p>

                        <h5 class="mt-4">Your Answers:</h5>

                        <ul class="list-group">
                            @foreach($result->answers as $questionId => $answer)
                            @php
                                $q = $questions[$questionId];
                                $correctAnswer = $q->correct_answer;
                                $isCorrect = $answer == $correctAnswer;
                            @endphp
                            <li class="list-group-item">
                            <p><strong>Question {{ $loop->iteration }}:</strong> {{ $q->question_text }}</p>
                            <p>Your answer: 
                            <span style="color: {{ $isCorrect ? 'green' : 'red' }}">
                                {{ $answer !== '' ? $answer : 'Not Answered' }}
                            </span>
            </p>
            <p>Correct answer: <strong>{{ $correctAnswer }}</strong></p>
        </li>
    @endforeach
</ul>
                    @else
                        <p class="text-danger">No assessment results found.</p>
                    @endif

                    <div class="mt-4 text-center">
                        @if(auth()->user()->role === 'user')
                            <a href="{{ route('assessment.start') }}" class="btn btn-primary">
                                Retake Assessment
                            </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
