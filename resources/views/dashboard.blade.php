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
            background: #f0f2f5;
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.4rem;
        }
        .card-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            font-weight: 500;
        }
        .card-body h5 {
            font-weight: 500;
        }
        .badge-status, .badge-score {
            font-size: 0.9rem;
        }
        .stats-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .invite-tabs .nav-link.active {
            background-color: #2575fc;
            color: #fff;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <!-- Left side: Logo + Text -->
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('images/logo_sq.png') }}" alt="Logo" width="40" height="40" class="me-2">
            <span class="fw-bold fs-5">Admin Dashboard</span>
        </a>

        <!-- Right side: Logout Button -->
        <div class="ms-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-5">

    <!-- Dashboard Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card stats-card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h5>Total Users</h5>
                    <h3>{{ $totalUsers }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                    <h5>Total Resumes</h5>
                    <h3>{{ $totalResumes }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h5>Completed Assessments</h5>
                    <h3>{{ $usersWithAssessment }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h5>Pending Resumes</h5>
                    <h3>{{ $totalResumes - $usersWithAssessment }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Invite Users -->
    <div class="card shadow-sm mb-5">
        <div class="card-header">
            <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i> Invite Users</h4>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs invite-tabs mb-3" id="inviteTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="csv-tab" data-bs-toggle="tab" data-bs-target="#csv" type="button">CSV Upload</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button">Single Invite</button>
                </li>
            </ul>
            <div class="tab-content" id="inviteTabContent">
                <!-- CSV Upload -->
                <div class="tab-pane fade show active" id="csv">
                    <form action="{{ route('admin.invite.csv') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Upload CSV (column: valid_email)</label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Upload & Invite</button>
                    </form>
                </div>
                <!-- Single Invite -->
                <div class="tab-pane fade" id="single">
                    <form action="{{ route('admin.invite.single') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Invite Single User</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="user@example.com" required>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-user-plus me-1"></i> Invite User</button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    <!-- Assessment Results -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0"><i class="fas fa-table me-2"></i> Assessment Results Overview</h4>
        </div>
        <div class="card-body">
            @if($results->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Resume Status</th>
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
                                        @php
                                            $resume = $resumes[$result->user_id] ?? null;
                                            $status = 'Pending';
                                            $badgeClass = 'bg-warning';

                                            if ($resume) {
                                                if ($resume->status == 'valid') {
                                                    $status = 'Valid';
                                                    $badgeClass = 'bg-success';
                                                } elseif ($resume->status == 'invalid') {
                                                    $status = 'Invalid';
                                                    $badgeClass = 'bg-danger';
                                                }
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary badge-score">{{ $result->correct_answers }} / {{ $result->total_questions }}</span>
                                    </td>
                                    <td>{{ $result->correct_answers }}</td>
                                    <td>{{ $result->wrong_answers }}</td>
                                    <td>
                                        <a href="{{ route('assessment.results.view', $result->id) }}" class="btn btn-sm btn-outline-primary me-1" title="View Result">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('assessment.download', $result->id) }}" class="btn btn-sm btn-outline-success" title="Download Report">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if($resume)
                                        <a href="{{ route('resume.download', $resume->id) }}" class="btn btn-sm btn-outline-info" title="Download Resume">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @endif
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
