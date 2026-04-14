<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Mobay</title>
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

        .forgot-container {
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
            margin-top: -50px;
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
            margin-top: -50px;
        }

        .description {
            font-size: 13px;
            color: #666;
            text-align: center;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .forgot-form {
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

        .submit-btn {
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

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            font-size: 14px;
        }

        .back-link a {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
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
            .forgot-container {
                padding: 30px 20px;
            }

            .logo {
                width: 110px;
                height: 110px;
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
            <div class="logo-subtitle">Lupa Password?</div>
        </div>
    </div>

    <!-- Forgot Password Card -->
    <div class="forgot-container">
        <!-- Alert Messages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <p class="description">
            Tidak masalah. Beri tahu kami email Anda dan kami akan mengirimkan tautan reset password untuk memilih password baru.
        </p>

        <!-- Forgot Password Form -->
        <form class="forgot-form" method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email Anda" 
                       value="{{ old('email') }}" required autofocus>
            </div>

            <button type="submit" class="submit-btn">Kirim Link Reset Password</button>
        </form>

        <!-- Back to Login Link -->
        <div class="back-link">
            <a href="{{ route('login') }}">← Kembali ke Login</a>
        </div>
    </div>
</body>
</html>
