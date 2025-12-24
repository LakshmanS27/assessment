<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Management System | Login</title>
 
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
 
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
 
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            transition: background 0.3s ease;
        }
 
        .login-container {
            min-height: 100vh;
        }
 
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 3rem 2.5rem;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            position: relative;
            animation: fadeInUp 0.6s ease-in-out;
        }
 
        .login-title {
            font-weight: 700;
            color: #1f2937;
        }
 
        .login-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }
 
        /* Microsoft Official Button Style */
        .btn-microsoft {
            background: #ffffff;
            border: 1px solid #d1d5db;
            color: #1f2937;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
 
        .btn-microsoft:hover {
            background: #f9fafb;
            border-color: #9ca3af;
            color: #111827;
        }
 
        .btn-microsoft.disabled {
            pointer-events: none;
            opacity: 0.7;
        }
 
        .footer-text {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #9ca3af;
        }
 
        /* Light Mode */
        body.light-mode {
            background: #f3f4f6;
        }
 
        body.light-mode .login-card {
            background: #ffffff;
        }
 
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
 
<body>
<div class="container login-container d-flex justify-content-center align-items-center">
    <div class="login-card text-center">
 
        <!-- Dark / Light Toggle -->
        <div class="form-check form-switch position-absolute top-0 end-0 m-3">
            <input class="form-check-input" type="checkbox" id="themeToggle">
        </div>
 
        <!-- Company Logo -->
        <div class="mb-3">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Company Logo"
                 width="80">
        </div>
 
        <!-- Title -->
        <h2 class="login-title">Assessment Management System</h2>
        <p class="login-subtitle">
            Secure sign-in using your Microsoft account
        </p>
 
        <!-- Microsoft Login Button -->
        <a href="{{ route('login.microsoft') }}"
           id="microsoftLoginBtn"
           class="btn btn-microsoft d-flex align-items-center justify-content-center gap-3 w-100">
 
            <!-- Official Microsoft Logo -->
            <img
                src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg"
                alt="Microsoft Logo"
                width="22"
                height="22"
            >
 
            <span class="btn-text">Sign in with Microsoft</span>
 
            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
        </a>
 
        <!-- Footer -->
        <div class="footer-text">
            © {{ date('Y') }} Assessment Platform. All rights reserved.
        </div>
    </div>
</div>
 
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
 
<!-- Custom JS -->
<script>
    // Spinner + disable button on click
    const loginBtn = document.getElementById('microsoftLoginBtn');

        if (loginBtn) {
            loginBtn.addEventListener('click', function () {
                loginBtn.classList.add('disabled');
                loginBtn.querySelector('.btn-text').textContent = 'Redirecting…';
                loginBtn.querySelector('.spinner-border').classList.remove('d-none');
            });
        }
 
    // Dark / Light mode toggle
    const toggle = document.getElementById('themeToggle');
    toggle.addEventListener('change', function () {
        document.body.classList.toggle('light-mode');
    });
</script>
 
</body>
</html>

