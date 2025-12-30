<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQ1 | Assessment Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0eafc, #cfdef3);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .hero-card {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }
        .brand-title {
            color: #6a11cb;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .sub-title {
            color: #2575fc;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            border: none;
            padding: 12px 40px;
            font-weight: 600;
            border-radius: 30px;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
        }
        .logo-img {
            max-width: 120px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 text-center">
            <div class="hero-card">
                <img src="{{ asset('images/sq1_logo.png') }}" alt="SQ1 Logo" class="logo-img">

                <h1 class="brand-title h3 mb-1">SQ1 SECURITY TECHNOLOGIES</h1>
                <h2 class="sub-title h5">ASSESSMENT MANAGEMENT SYSTEM</h2>

                <hr class="my-4 mx-auto" style="width: 50px; border-top: 3px solid #6a11cb;">

                <p class="lead text-dark fw-bold">
                    Welcome to SQ1.
                </p>
                <p class="text-muted">
                    Proceed to login to access the login portal. 
                    We are here to provide you a smooth assessment to pick out the best talent in you.
                </p>

                <div class="mt-4">
                    <a href="{{ route('login') }}" class="btn btn-login text-white">Login to Portal</a>
                </div>
            </div>
            
            <footer class="mt-4 text-muted small">
                &copy; {{ date('Y') }} SQ1 Security Technologies. All rights reserved.
            </footer>
        </div>
    </div>
</div>

</body>
</html>