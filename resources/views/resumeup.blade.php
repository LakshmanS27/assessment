<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <!-- Logout Button -->
            <div class="d-flex justify-content-end mb-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
            </div>

            <!-- Upload Card -->
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center rounded-top-4">
                    <h3 class="mb-0">Upload Your Resume</h3>
                    <p class="mb-0 small">PDF files only</p>
                </div>
                <div class="card-body p-4">

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Upload Form -->
                    <form action="{{ route('resume.upload.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="resume" class="form-label">Choose Resume (PDF)</label>
                            <input type="file"
                                   class="form-control form-control-lg"
                                   id="resume"
                                   name="resume"
                                   accept=".pdf"
                                   required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Upload Resume</button>
                    </form>
                </div>
            </div>

            <!-- RESULT CARD -->
           @if(session('result'))
    <div class="card mt-4 shadow border-0">
        <div class="card-body">
            <!-- Existing resume status and matched skills display -->

            <h5 class="mb-3">
                Resume Status:
                <span class="badge 
                    {{ session('result.status') === 'valid' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst(session('result.status')) }}
                </span>
            </h5>

            <!-- Match Percentage and progress bar -->
            <p><strong>Match Percentage:</strong> {{ session('result.percentage') }}%</p>

            <div class="progress mb-3">
                <div class="progress-bar 
                    {{ session('result.status') === 'valid' ? 'bg-success' : 'bg-danger' }}"
                    style="width: {{ session('result.percentage') }}%">
                    {{ session('result.percentage') }}%
                </div>
            </div>

            <!-- Matched Skills -->
            <p><strong>Matched Skills:</strong></p>
            @if(count(session('result.matched_skills')) > 0)
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach(session('result.matched_skills') as $skill)
                        <span class="badge bg-secondary">{{ ucfirst($skill) }}</span>
                    @endforeach
                </div>
            @else
                <p class="text-muted">No matching skills found.</p>
            @endif

            <!-- Button or message -->
            <hr>

            @if(session('result.status') === 'valid')
            <div class="d-grid">
                <a href="{{ route('assessment.start') }}" class="btn btn-success btn-lg">
                    Proceed to Assessment
                </a>
            </div>
            @else
            <div class="alert alert-warning text-center mb-0">
                Your resume did not meet the criteria.  
                <strong>Please try uploading again.</strong>
             </div>
            @endif


        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
