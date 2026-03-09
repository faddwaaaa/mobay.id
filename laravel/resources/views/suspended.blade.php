<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akun Ditangguhkan — Payou.id</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f0f4ff;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}
.wrap {
    background: white;
    border-radius: 20px;
    border: 1.5px solid #e4ecff;
    padding: 52px 48px;
    max-width: 480px;
    width: 100%;
    text-align: center;
    box-shadow: 0 8px 40px rgba(35,86,232,.07);
}
.icon-wrap {
    width: 72px; height: 72px;
    background: #fee2e2;
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px;
}
.icon-wrap svg { color: #b91c1c; }
h1 {
    font-size: 22px; font-weight: 900;
    color: #0c1533; letter-spacing: -.4px;
    margin-bottom: 10px;
}
.sub {
    font-size: 14px; color: #7a8db5;
    line-height: 1.65; font-weight: 500;
    margin-bottom: 28px;
}
.info-box {
    background: #fff7ed;
    border: 1.5px solid #fed7aa;
    border-radius: 12px;
    padding: 16px 20px;
    text-align: left;
    margin-bottom: 28px;
}
.info-box p {
    font-size: 12.5px; font-weight: 700;
    color: #c2410c; margin-bottom: 6px;
}
.info-box ul {
    list-style: none; padding: 0;
}
.info-box ul li {
    font-size: 12px; color: #92400e;
    font-weight: 600; padding: 3px 0;
    display: flex; align-items: flex-start; gap: 7px;
}
.info-box ul li::before {
    content: '';
    width: 5px; height: 5px;
    background: #f97316;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
}
.btn-logout {
    display: inline-flex; align-items: center; gap: 7px;
    background: #1a3fa8; color: white;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; font-weight: 800;
    padding: 11px 24px; border-radius: 11px;
    border: none; cursor: pointer;
    text-decoration: none;
    transition: background .15s;
    margin-bottom: 14px;
    width: 100%; justify-content: center;
}
.btn-logout:hover { background: #153090; }
.contact {
    font-size: 12px; color: #7a8db5; font-weight: 600;
}
.contact a { color: #2356e8; text-decoration: none; font-weight: 700; }
.contact a:hover { text-decoration: underline; }
.logo {
    font-size: 13px; font-weight: 900;
    color: #c2cfe8; margin-bottom: 32px;
    letter-spacing: -.2px;
}
</style>
</head>
<body>
<div class="wrap">
    <div class="logo">Payou.id</div>

    <div class="icon-wrap">
        <svg width="34" height="34" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>

    <h1>Akun Anda Ditangguhkan</h1>
    <p class="sub">
        Akun Anda telah ditangguhkan oleh tim moderasi Payou.id karena melanggar ketentuan layanan kami.
        Selama masa penangguhan, semua fitur dan toko Anda tidak dapat diakses.
    </p>

    <div class="info-box">
        <p>Selama penangguhan, Anda tidak dapat:</p>
        <ul>
            <li>Mengakses dashboard dan pengaturan akun</li>
            <li>Menerima pembayaran atau transaksi baru</li>
            <li>Profil publik dan toko Anda tidak dapat dibuka pengunjung</li>
            <li>Melakukan penarikan saldo</li>
        </ul>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Keluar dari Akun
        </button>
    </form>

    <p class="contact">
        Merasa ini adalah kesalahan?
        <a href="mailto:support@payou.id">Hubungi tim support</a>
    </p>
</div>
</body>
</html>