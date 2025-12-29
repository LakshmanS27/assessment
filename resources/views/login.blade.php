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
            background: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #111827;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .login-container {
            width: 100%;
            max-width: 500px;
            padding: 3rem;
        }

        .login-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 4rem 3rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
            position: relative;
            text-align: center;
            animation: fadeInUp 0.6s ease-in-out;
            border-top: 6px solid #4f46e5;
            transition: background 0.3s ease, color 0.3s ease, border-top 0.3s ease;
        }

        .login-title { font-weight: 700; font-size: 2rem; }
        .login-subtitle { color: #6b7280; font-size: 1rem; margin-bottom: 2.5rem; }

        /* Microsoft Button */
        .btn-microsoft {
            background: #f3f4f6;
            border: none;
            color: #111827;
            font-weight: 600;
            padding: 0.9rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.2s ease;
        }

        .btn-microsoft:hover { background: #e5e7eb; color: #111827; }
        .btn-microsoft.disabled { pointer-events: none; opacity: 0.6; }

        .footer-text { margin-top: 2.5rem; font-size: 0.85rem; color: #9ca3af; }

        /* Dark Mode */
        body.dark-mode { background: #1e293b; color: #f1f5f9; }
        body.dark-mode .login-card { background: #2d3748; border-top-color: #6366f1; color: #f1f5f9; }
        body.dark-mode .login-subtitle { color: #cbd5e1; }
        body.dark-mode .btn-microsoft { background: #4b5563; color: #f1f5f9; }
        body.dark-mode .btn-microsoft:hover { background: #6b7280; color: #f1f5f9; }
        body.dark-mode .footer-text { color: #94a3b8; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="login-card">

        <!-- Dark / Light Toggle -->
        <div class="form-check form-switch position-absolute top-0 end-0 m-3">
            <input class="form-check-input" type="checkbox" id="themeToggle">
        </div>

        <!-- Company Logo -->
        <div class="mb-5 mt-2">
            <img src="{{ asset('images/logo_sq.png') }}" alt="Company Logo" width="100" class="mx-auto d-block">
        </div>

        <h2 class="login-title">Assessment Management System</h2>
        <p class="login-subtitle">Secure sign-in using your Microsoft account</p>

        <!-- Invite Error Alert -->
        @if(session('invite_error'))
            <div class="alert alert-danger mb-4">
                {{ session('invite_error') }}
            </div>
        @endif

        <!-- Microsoft Login Button -->
        <a href="{{ route('login.microsoft') }}" id="microsoftLoginBtn" class="btn btn-microsoft w-100">
            <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg" alt="Microsoft Logo" width="22" height="22">
            <span class="btn-text">Sign in with Microsoft</span>
            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
        </a>

        <!-- Footer -->
        <div class="footer-text">
            © {{ date('Y') }} Assessment Platform. All rights reserved.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const loginBtn = document.getElementById('microsoftLoginBtn');
    if (loginBtn) {
        loginBtn.addEventListener('click', function () {
            loginBtn.classList.add('disabled');
            loginBtn.querySelector('.btn-text').textContent = 'Redirecting…';
            loginBtn.querySelector('.spinner-border').classList.remove('d-none');
        });
    }

    const toggle = document.getElementById('themeToggle');
    toggle.addEventListener('change', function () {
        document.body.classList.toggle('dark-mode');
    });
</script>
</body>
</html>
