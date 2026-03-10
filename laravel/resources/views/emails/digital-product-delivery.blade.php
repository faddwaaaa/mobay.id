<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Siap Didownload</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Helvetica Neue',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                {{-- LOGO --}}
                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        <img src="{{ url('img/logo.png') }}" alt="Payou.id" style="height:44px;display:block;">
                    </td>
                </tr>

                {{-- HERO CARD --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#1d4ed8,#2563eb,#3b82f6);border-radius:20px 20px 0 0;padding:40px 32px;text-align:center;">

                        {{-- Check icon SVG --}}
                        <div style="width:72px;height:72px;background:rgba(255,255,255,.2);border-radius:20px;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;">
                            <img src="https://img.icons8.com/ios-filled/72/ffffff/checked--v1.png" width="36" height="36" alt="success" style="display:block;margin:18px auto 0;">
                        </div>

                        <h1 style="color:#fff;font-size:26px;font-weight:800;margin:0 0 8px;">Pembayaran Berhasil!</h1>
                        <p style="color:rgba(255,255,255,.85);font-size:15px;margin:0;">File produk digital kamu sudah siap didownload</p>
                    </td>
                </tr>

                {{-- BODY --}}
                <tr>
                    <td style="background:#fff;padding:32px;border-radius:0 0 20px 20px;box-shadow:0 20px 60px rgba(15,23,42,.1);">

                        <p style="font-size:16px;color:#0f172a;margin:0 0 24px;">
                            Halo <strong>{{ $buyerName }}</strong>,
                        </p>
                        <p style="font-size:15px;color:#475569;line-height:1.7;margin:0 0 28px;">
                            Terima kasih sudah melakukan pembelian di <strong style="color:#2563eb;">Payou.id</strong>. Pembayaranmu telah dikonfirmasi dan file siap untuk didownload.
                        </p>

                        {{-- Detail pesanan --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:16px 20px;border-bottom:1px solid #e2e8f0;">
                                    <p style="font-size:12px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin:0 0 12px;">Detail Pesanan</p>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-size:14px;color:#64748b;padding:5px 0;width:120px;">Produk</td>
                                            <td style="font-size:14px;color:#0f172a;font-weight:700;padding:5px 0;">{{ $productName }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:14px;color:#64748b;padding:5px 0;">Order ID</td>
                                            <td style="font-size:14px;color:#0f172a;font-weight:700;padding:5px 0;">#{{ $order->order_code }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:14px;color:#64748b;padding:5px 0;">Email</td>
                                            <td style="font-size:14px;color:#0f172a;font-weight:700;padding:5px 0;">{{ $order->buyer_email }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- Tombol download --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $downloadUrl }}"
                                       style="display:inline-block;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;text-decoration:none;font-size:16px;font-weight:800;padding:16px 40px;border-radius:14px;box-shadow:0 8px 24px rgba(37,99,235,.35);">
                                        Download File Sekarang
                                    </a>
                                </td>
                            </tr>
                        </table>

                        {{-- Info penting --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#fefce8;border:1.5px solid #fde68a;border-radius:14px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="font-size:13px;font-weight:800;color:#92400e;margin:0 0 10px;">Perhatian Penting</p>
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:3px 0;vertical-align:top;">
                                                <span style="display:inline-block;width:6px;height:6px;background:#d97706;border-radius:50%;margin:6px 10px 0 0;vertical-align:top;"></span>
                                            </td>
                                            <td style="font-size:13px;color:#92400e;padding:3px 0;line-height:1.6;">
                                                Link hanya bisa diakses oleh email: <strong>{{ $order->buyer_email }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:3px 0;vertical-align:top;">
                                                <span style="display:inline-block;width:6px;height:6px;background:#d97706;border-radius:50%;margin:6px 10px 0 0;vertical-align:top;"></span>
                                            </td>
                                            <td style="font-size:13px;color:#92400e;padding:3px 0;line-height:1.6;">
                                                Link berlaku hingga: <strong>{{ $expiresAt }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:3px 0;vertical-align:top;">
                                                <span style="display:inline-block;width:6px;height:6px;background:#d97706;border-radius:50%;margin:6px 10px 0 0;vertical-align:top;"></span>
                                            </td>
                                            <td style="font-size:13px;color:#92400e;padding:3px 0;line-height:1.6;">
                                                Maksimal download: <strong>{{ $maxDownload }}x</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:3px 0;vertical-align:top;">
                                                <span style="display:inline-block;width:6px;height:6px;background:#d97706;border-radius:50%;margin:6px 10px 0 0;vertical-align:top;"></span>
                                            </td>
                                            <td style="font-size:13px;color:#92400e;padding:3px 0;line-height:1.6;">
                                                Jangan bagikan link ini ke orang lain
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- Fallback URL --}}
                        <p style="font-size:13px;color:#94a3b8;margin:0 0 8px;">Jika tombol tidak bisa diklik, copy link berikut ke browser:</p>
                        <p style="font-size:12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;word-break:break-all;color:#475569;margin:0;">
                            {{ $downloadUrl }}
                        </p>

                    </td>
                </tr>

                {{-- FOOTER --}}
                <tr>
                    <td align="center" style="padding:24px 0 8px;">
                        <p style="font-size:12px;color:#94a3b8;margin:0 0 4px;">Email ini dikirim otomatis oleh <strong style="color:#64748b;">Payou.id</strong></p>
                        <p style="font-size:12px;color:#94a3b8;margin:0;">Ada masalah? Hubungi CS kami di
                            <a href="mailto:smeganemolab@gmail.com" style="color:#2563eb;text-decoration:none;font-weight:700;">smeganemolab@gmail.com</a>
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>