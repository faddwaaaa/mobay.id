<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Payou</title>
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
            gap: 0;
        }

        /* ── Logo wrapper (outside card) ── */
        .logo-wrapper {
            text-align: center;
            animation: slideDown 0.6s ease;
            margin-bottom: 20px;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo {
            width: 160px;
            height: 70px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            height: 55px;
            width: auto;
            object-fit: contain;
        }

        .logo-subtitle {
            font-size: 14px;
            color: #555;
            margin-top: 8px;
            margin-bottom: 10px;
        }

        /* ── Card ── */
        .register-container {
            background: white;
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(30, 136, 229, 0.15);
            width: 100%;
            max-width: 400px;
            padding: 36px 40px;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Form ── */
        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 7px;
        }

        .form-group input {
            width: 100%;
            padding: 11px 14px;
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
            color: #bbb;
        }

        .register-btn {
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
            margin-bottom: 14px;
            margin-top: 4px;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.3);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 14px 0;
            color: #bbb;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e8e8e8;
        }

        .divider::before { margin-right: 10px; }
        .divider::after  { margin-left: 10px; }

        .google-btn {
            width: 100%;
            padding: 11px;
            border: 2px solid #e0e0e0;
            background: white;
            color: #333;
            border-radius: 8px;
            font-size: 14px;
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

        .google-icon-img {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            object-fit: contain;
        }

        .login-link {
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
            color: #666;
        }

        .login-link a {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            color: #1565c0;
        }

        .alert {
            padding: 11px 14px;
            margin-bottom: 16px;
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

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .logo img {
                height: 60px;
            }

            .logo {
                width: 160px;
                height: 70px;
            }

            .logo-subtitle {
                font-size: 14px;
            }

            .register-container {
                padding: 28px 20px;
            }
        }

        @media (max-width: 360px) {
            .logo img {
                height: 52px;
            }

            .logo {
                width: 140px;
                height: 60px;
            }
        }
    </style>
</head>
<body>

    <!-- Logo Outside Card -->
    <div class="logo-wrapper">
        <div class="logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo Payou">
        </div>
        <p class="logo-subtitle">Buat akun baru Anda</p>
    </div>

    <!-- Register Card -->
    <div class="register-container">

        @if ($errors->any())
            <div class="alert alert-danger">
                Pendaftaran gagal. Pastikan semua data sudah diisi dengan benar.
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name"
                       placeholder="Masukkan nama lengkap"
                       value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Contoh: admin123"
                       value="{{ old('username') }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       placeholder="Masukkan email Anda"
                       value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Minimal 8 karakter" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="Ulangi password Anda" required>
            </div>

            <button type="submit" class="register-btn">Daftar</button>
        </form>

        <div class="divider">atau</div>

        <a href="{{ route('auth.google') }}" class="google-btn">
            <img src="../img/google.png" alt="Google" class="google-icon-img">
            Daftar dengan Google
        </a>

        <div class="login-link">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>

    </div>
</body>
</html>