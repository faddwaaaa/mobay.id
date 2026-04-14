<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Produk Digital Anda</title>
</head>
<body style="font-family:system-ui,sans-serif;background:#f9fafb;padding:32px 16px;">
    <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
        <div style="background:#2563eb;padding:24px;text-align:center;color:white;">
            <div style="font-size:36px;margin-bottom:8px;">📦</div>
            <h1 style="font-size:20px;margin:0;">Produk Digital Anda Siap!</h1>
        </div>
        <div style="padding:24px;">
            <p style="font-size:15px;color:#374151;margin-bottom:16px;">
                Halo <strong>{{ $buyerName }}</strong>, terima kasih telah melakukan pembelian!
            </p>
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:16px;margin-bottom:16px;">
                <p style="font-size:14px;color:#1d4ed8;margin:0 0 4px;">
                    <strong>Produk:</strong> {{ $productTitle }}
                </p>
                <p style="font-size:14px;color:#1d4ed8;margin:0;">
                    <strong>No. Order:</strong> {{ $orderId }}
                </p>
            </div>
            <p style="font-size:14px;color:#374151;margin-bottom:8px;">
                File produk digital Anda terlampir dalam email ini. Silakan unduh dan simpan di tempat yang aman.
            </p>
            <p style="font-size:12px;color:#9ca3af;margin-top:24px;">
                Email ini dikirim otomatis. Jika ada pertanyaan, balas email ini atau hubungi penjual.
            </p>
        </div>
    </div>
</body>
</html>