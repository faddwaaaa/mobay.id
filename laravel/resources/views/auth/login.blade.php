<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Payou</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 50%, #1e88e5 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            gap: 30px;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(30, 136, 229, 0.15);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            animation: slideUp 0.5s ease;
            margin-top: -20px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-wrapper {
            animation: slideDown 0.6s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 250px;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: -60px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-section {
            text-align: center;
        }

        .logo-subtitle {
            font-size: 15px;
            color: #666;
            text-align: center;
            margin: 0;
            margin-top: -70px;
        }

        .login-form {
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1e88e5;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .form-group input::placeholder {
            color: #999;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .remember-forgot a {
            color: #1e88e5;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .remember-forgot a:hover {
            color: #1565c0;
            text-decoration: underline;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            color: #666;
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            width: auto;
            margin-right: 6px;
            cursor: pointer;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1e88e5, #42a5f5);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #999;
            font-size: 13px;
            margin-top: 5px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }

        .divider::before {
            margin-right: 10px;
        }

        .divider::after {
            margin-left: 10px;
        }

        .google-btn {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            background: white;
            color: #333;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .google-btn:hover {
            border-color: #1e88e5;
            background: #f5f9ff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 136, 229, 0.15);
        }

        .google-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="20" fill="%234285F4">G</text></svg>') center/contain no-repeat;
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .signup-link a {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .signup-link a:hover {
            color: #1565c0;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .logo {
                width: 110px;
                height: 110px;
            }

            .logo-text {
                font-size: 24px;
            }

            body {
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Logo Outside Card -->
    <div class="logo-wrapper">
        <div class="logo-section">
            <div class="logo">
                <img src="{{ asset('img/icon.png') }}" alt="Logo">
            </div>
            <div class="logo-subtitle">Masuk ke akun Anda</div>
        </div>
    </div>

    <!-- Login Card -->
    <div class="login-container">
        <!-- Alert Messages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Login Form -->
        <form class="login-form" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email Anda" 
                       value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
            </div>

            <div class="remember-forgot">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" id="remember">
                    Ingat saya
                </label>
                <a href="{{ route('password.request') }}">Lupa password?</a>
            </div>

            <button type="submit" class="login-btn">Masuk</button>
        </form>

        <!-- Divider -->
        <div class="divider">atau</div>

        <!-- Google Login Button -->
        <a href="{{ route('auth.google') }}" class="google-btn">
            <span class="google-icon"></span>
            Masuk dengan Google
        </a>

        <!-- Sign Up Link -->
        <div class="signup-link">
            Belum punya akun? <a href="/register">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
