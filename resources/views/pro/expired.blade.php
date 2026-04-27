<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Langganan Pro Habis - Mobay.id</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.16), transparent 28%),
                radial-gradient(circle at bottom right, rgba(249, 115, 22, 0.16), transparent 24%),
                linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%);
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 860px;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid #dbeafe;
            border-radius: 28px;
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.12);
            overflow: hidden;
        }
        .hero {
            padding: 32px 32px 24px;
            background: linear-gradient(135deg, #eff6ff 0%, #fff7ed 100%);
            border-bottom: 1px solid #e2e8f0;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #fff;
            color: #c2410c;
            border: 1px solid #fdba74;
            font-size: 12px;
            font-weight: 800;
        }
        h1 {
            margin: 18px 0 10px;
            font-size: 34px;
            line-height: 1.1;
            letter-spacing: -0.03em;
        }
        .sub {
            margin: 0;
            max-width: 640px;
            font-size: 15px;
            line-height: 1.8;
            color: #475569;
        }
        .body {
            padding: 28px 32px 32px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 18px;
        }
        .panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            padding: 22px;
        }
        .panel-title {
            margin: 0 0 14px;
            font-size: 15px;
            font-weight: 800;
        }
        .info-list {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .info-list li {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            color: #475569;
            font-size: 13px;
            line-height: 1.7;
        }
        .info-list i {
            width: 18px;
            color: #ea580c;
            margin-top: 2px;
        }
        .detail-box {
            padding: 14px 16px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 12px;
        }
        .detail-label {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #64748b;
        }
        .detail-value {
            margin-top: 6px;
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }
        .detail-copy {
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.7;
            color: #64748b;
        }
        .action-stack {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }
        .action-btn, .ghost-btn {
            width: 100%;
            border: none;
            border-radius: 16px;
            min-height: 50px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .action-btn {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            color: #fff;
            box-shadow: 0 16px 34px rgba(37, 99, 235, 0.22);
        }
        .ghost-btn {
            background: #fff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .logout-form {
            margin-top: 12px;
        }
        .testing-note {
            margin-top: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #eff6ff;
            border: 1px dashed #93c5fd;
            color: #1d4ed8;
            font-size: 12px;
            line-height: 1.7;
        }
        @media (max-width: 780px) {
            .hero, .body { padding: 24px 20px; }
            .grid { grid-template-columns: 1fr; }
            h1 { font-size: 28px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="hero">
            <div class="badge">
                <i class="fas fa-hourglass-end"></i>
                Langganan Pro Berakhir
            </div>
            <h1>Akun Pro Anda sedang tidak aktif.</h1>
            <p class="sub">
                Setelah fitur Pro pernah aktif, akun ini tidak bisa kembali ke mode Free. Untuk melanjutkan aktivitas di web, silakan lakukan pembayaran Pro kembali.
            </p>
        </div>

        <div class="body">
            <div class="grid">
                <div class="panel">
                    <h2 class="panel-title">Selama status ini aktif, akun tidak dapat:</h2>
                    <ul class="info-list">
                        <li><i class="fas fa-ban"></i><span>Mengakses dashboard dan fitur operasional lainnya.</span></li>
                        <li><i class="fas fa-store-slash"></i><span>Menampilkan profil publik dan toko ke pengunjung.</span></li>
                        <li><i class="fas fa-chart-line"></i><span>Menggunakan fitur Pro sampai pembayaran diperpanjang.</span></li>
                    </ul>

                    <div class="action-stack">
                        <button type="button" class="action-btn" onclick="showProQrisModal('monthly')">
                            <i class="fas fa-qrcode"></i>
                            Bayar Pro Bulanan
                        </button>
                        <button type="button" class="ghost-btn" onclick="showProQrisModal('yearly')">
                            <i class="fas fa-crown"></i>
                            Bayar Pro Tahunan
                        </button>
                        <a href="{{ route('premium.index') }}" class="ghost-btn">
                            <i class="fas fa-arrow-up-right-from-square"></i>
                            Lihat Detail Paket
                        </a>
                    </div>
                </div>

                <div class="panel">
                    <div class="detail-box">
                        <div class="detail-label">Akun</div>
                        <div class="detail-value">{{ '@' . $user->username }}</div>
                        <div class="detail-copy">{{ $user->name }}</div>
                    </div>
                    <div class="detail-box">
                        <div class="detail-label">Berakhir Pada</div>
                        <div class="detail-value">{{ optional($user->pro_until)->format('d M Y H:i') ?? '-' }}</div>
                        <div class="detail-copy">Aktifkan kembali Pro agar akses akun pulih otomatis.</div>
                    </div>
                    <div class="detail-box">
                        <div class="detail-label">Status Sekarang</div>
                        <div class="detail-value">Pro Habis</div>
                        <div class="detail-copy">Status ini akan tetap mengunci akun sampai ada pembayaran Pro baru.</div>
                    </div>

                    <div class="testing-note">
                        Untuk testing, akun yang sudah masuk status Pro habis tetap harus mengikuti alur expired ini. Reaktivasi hanya bisa lewat pembayaran Pro baru.
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="ghost-btn">
                            <i class="fas fa-right-from-bracket"></i>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('pro.qris-modal')
</body>
</html>
