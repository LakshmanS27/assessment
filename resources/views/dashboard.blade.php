<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>
    <style>
        body {
            background: #f8f9fa;
        }
        .card-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .badge-score {
            font-size: 1rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <div class="ms-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="mb-0">Assessment Results Overview</h3>
        </div>
        <div class="card-body">
            @if($results->count())
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Score</th>
                                <th>Correct</th>
                                <th>Wrong</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $result->user->name }}</td>
                                    <td>{{ $result->user->email }}</td>
                                    <td>{{ $result->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-success badge-score">
                                            {{ $result->correct_answers }} / {{ $result->total_questions }}
                                        </span>
                                    </td>
                                    <td>{{ $result->correct_answers }}</td>
                                    <td>{{ $result->wrong_answers }}</td>
                                    <td>
                                        <a href="{{ route('assessment.results', ['id' => $result->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted">No assessments taken yet.</p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
