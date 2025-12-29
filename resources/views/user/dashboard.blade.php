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

    <div class="d-flex justify-content-between mb-4">
        <h2>Welcome, {{ auth()->user()->name }}</h2>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">Logout</button>
        </form>
    </div>

    <div class="row g-4">
        <!-- Resume Status -->
        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-header bg-primary text-white">Resume Status</div>
                <div class="card-body">
                    <h5 class="card-title">
                        @if($resumeStatus === 'valid')
                            <span class="badge bg-success">Uploaded & Valid</span>
                        @elseif($resumeStatus === 'invalid')
                            <span class="badge bg-danger">Uploaded but Invalid</span>
                        @else
                            <span class="badge bg-warning">Not Uploaded</span>
                        @endif
                    </h5>
                    @if(!$resumeStatus)
                        <a href="{{ route('resume.upload.form') }}" class="btn btn-primary mt-2">Upload Resume</a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assessment Status -->
        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-header bg-success text-white">Assessment Status</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $assessmentStatus }}</h5>

                    @if($inProgress)
                        <a href="{{ route('assessment.start') }}" class="btn btn-warning mt-2">Continue Assessment</a>
                    @elseif($assessmentStatus === 'Not Started' && $resumeStatus === 'valid')
                        <a href="{{ route('assessment.start') }}" class="btn btn-primary mt-2">Start Assessment</a>
                    @elseif($assessmentStatus === 'Completed')
                        <a href="{{ route('assessment.results') }}" class="btn btn-success mt-2">View Results</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
