<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode OTP Download</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
    <tr>
        <td align="center">
            <table width="520" cellpadding="0" cellspacing="0" style="max-width:520px;width:100%;">

                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        <img src="{{ url('img/logo.png') }}" alt="Mobay.id" style="height:40px;display:block;">
                    </td>
                </tr>

                <tr>
                    <td style="background:linear-gradient(135deg,#1d4ed8,#2563eb);border-radius:20px 20px 0 0;padding:36px 32px;text-align:center;">
                        <h1 style="color:#fff;font-size:24px;font-weight:800;margin:0 0 8px;">Kode OTP Download</h1>
                        <p style="color:rgba(255,255,255,.85);font-size:14px;margin:0;">Produk: <strong>{{ $productName }}</strong></p>
                    </td>
                </tr>

                <tr>
                    <td style="background:#fff;padding:32px;border-radius:0 0 20px 20px;box-shadow:0 20px 60px rgba(15,23,42,.1);">

                        <p style="font-size:15px;color:#475569;margin:0 0 24px;line-height:1.7;">
                            Gunakan kode berikut untuk mengunduh file produkmu. Kode berlaku selama <strong>10 menit</strong>.
                        </p>

                        <!-- OTP BOX -->
                        <div style="background:#eff6ff;border:2px dashed #93c5fd;border-radius:16px;padding:28px;text-align:center;margin-bottom:24px;">
                            <p style="font-size:13px;color:#64748b;margin:0 0 10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;">Kode OTP</p>
                            <p style="font-size:48px;font-weight:900;color:#1d4ed8;letter-spacing:12px;margin:0;">{{ $otp }}</p>
                            <p style="font-size:13px;color:#94a3b8;margin:10px 0 0;">Berlaku hingga pukul {{ $expiresAt }}</p>
                        </div>

                        <!-- WARNING -->
                        <div style="background:#fefce8;border:1.5px solid #fde68a;border-radius:12px;padding:14px 18px;margin-bottom:0;">
                            <p style="font-size:13px;color:#92400e;margin:0;line-height:1.6;">
                                ⚠️ Jangan bagikan kode ini ke siapapun. Jika kamu tidak merasa meminta kode ini, abaikan email ini.
                            </p>
                        </div>

                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:20px 0 8px;">
                        <p style="font-size:12px;color:#94a3b8;margin:0;">Email otomatis dari <strong style="color:#64748b;">Mobay.id</strong> &middot;
                            <a href="mailto:smeganemolab@gmail.com" style="color:#2563eb;text-decoration:none;">Hubungi CS</a>
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
