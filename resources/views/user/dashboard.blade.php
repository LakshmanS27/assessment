<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">

    {{-- Flash Error --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, {{ auth()->user()->name }}</h2>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">Logout</button>
        </form>
    </div>

    <div class="row g-4">

        {{-- Resume Status --}}
        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-header bg-primary text-white">
                    Resume Status
                </div>

                <div class="card-body">

                    @if($resumeStatus === 'valid')
                        <span class="badge bg-success mb-3">Uploaded & Valid</span>
                    @elseif($resumeStatus === 'invalid')
                        <span class="badge bg-danger mb-3">Uploaded & Invalid</span>
                    @else
                        <span class="badge bg-warning text-dark mb-3">Not Uploaded</span>
                    @endif

                    <div class="mt-3">
                        @if($resumeStatus !== 'valid')
                            <a href="{{ route('resume.upload.form') }}" class="btn btn-primary">
                                Upload / Re-upload Resume
                            </a>
                        @else
                            <span class="text-muted">Resume is valid. No action required.</span>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- Assessment Status --}}
        <div class="col-md-6">
    <div class="card text-center shadow-sm">
        <div class="card-header bg-success text-white">
            Assessment Status
        </div>
        <div class="card-body">
            {{-- Status Badge --}}
            @if($assessmentStatus === 'Completed')
                <span class="badge bg-success mb-3">{{ $assessmentStatus }}</span>
            @elseif($assessmentStatus === 'In Progress')
                <span class="badge bg-warning text-dark mb-3">{{ $assessmentStatus }}</span>
            @else
                <span class="badge bg-secondary mb-3">{{ $assessmentStatus }}</span>
            @endif

            {{-- Action Buttons --}}
            <div class="mt-3">
                @if($inProgress)
                    <a href="{{ route('assessment.start') }}" class="btn btn-warning btn-lg w-100">
                        Continue Assessment
                    </a>
                @elseif($assessmentStatus === 'Not Started' && $resumeStatus === 'valid')
                    <a href="{{ route('assessment.start') }}" class="btn btn-success btn-lg w-100">
                        Start Assessment
                    </a>
                @elseif($resumeStatus === 'invalid')
                    <button class="btn btn-secondary btn-lg w-100" disabled>
                        Start Assessment (Resume Invalid)
                    </button>
                @elseif($assessmentStatus === 'Completed')
                    <a href="{{ route('assessment.results') }}" class="btn btn-primary btn-lg w-100 mt-2">
                        View Results
                    </a>
                @else
                    <span class="text-muted d-block mt-2">Assessment not available</span>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
