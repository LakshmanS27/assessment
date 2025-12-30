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
                        <p><strong>Date:</strong> {{ $result->created_at->format('d M Y, H:i') }}</p>

                        <p class="fw-bold">
                            Score: {{ $result->correct_answers }} / {{ $result->total_questions }}
                        </p>

                        <p>
                            <strong>Violations:</strong>
                            <span class="badge 
                                {{ $result->violations >= 3 ? 'bg-danger' : ($result->violations > 0 ? 'bg-warning' : 'bg-success') }}">
                                {{ $result->violations }}
                            </span>
                        </p>

                        @if($result->violations >= 3)
                            <div class="alert alert-danger mt-3">
                                Your assessment was automatically submitted due to multiple rule violations.
                            </div>
                        @endif

                        <h5 class="mt-4">Your Answers:</h5>

                        <ul class="list-group mb-4">
                            @foreach($result->question_ids as $index => $questionId)
                                @php
                                    $q = $questions[$questionId] ?? null;
                                    $answer = $result->answers[$questionId] ?? '';
                                    $correctAnswer = $q->correct_answer ?? 'N/A';
                                    $isCorrect = $q
                                        ? trim(strtolower($answer)) === trim(strtolower($correctAnswer))
                                        : false;
                                @endphp

                                <li class="list-group-item">
                                    <p>
                                        <strong>Question {{ $index + 1 }}:</strong>
                                        {{ $q->question_text ?? 'Question not found' }}
                                    </p>
                                    <p>
                                        Your answer:
                                        <span style="color: {{ $isCorrect ? 'green' : 'red' }}">
                                            {{ $answer !== '' ? $answer : 'Not Answered' }}
                                        </span>
                                    </p>
                                    <p>
                                        Correct answer:
                                        <strong>{{ $correctAnswer }}</strong>
                                    </p>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-4 text-center">
                            <form action="{{ route('logout') }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-danger">Logout</button>
                            </form>
                        </div>

                    @else
                        <p class="text-danger">No assessment results found.</p>
                        <div class="mt-4 text-center">
                            <form action="{{ route('logout') }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-danger">Logout</button>
                            </form>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
