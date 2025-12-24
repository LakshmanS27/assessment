<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Upload</title>

    <link rel="stylesheet" href="{{ asset('index.css') }}">
</head>

<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Upload Your Resume</h4>
        </div>

        <div class="card-body">

            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('resume.upload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="resume">Choose Resume File</label>
                    <input type="file" name="resume" id="resume">
                    <small>Allowed formats: PDF, DOC, DOCX</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    Upload Resume
                </button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
