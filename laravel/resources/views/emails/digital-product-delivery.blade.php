<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Siap Didownload</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #3b82f6, #1d4ed8); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 24px; }
        .header p { color: #bfdbfe; margin: 8px 0 0; }
        .body { padding: 32px 24px; }
        .greeting { font-size: 16px; color: #374151; margin-bottom: 16px; }
        .product-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .product-box h3 { margin: 0 0 8px; color: #0369a1; font-size: 16px; }
        .product-box p { margin: 4px 0; color: #64748b; font-size: 14px; }
        .btn-download { display: block; background: #3b82f6; color: #fff !important; text-decoration: none; text-align: center; padding: 16px 24px; border-radius: 8px; font-size: 16px; font-weight: bold; margin: 24px 0; }
        .btn-download:hover { background: #1d4ed8; }
        .info-box { background: #fefce8; border: 1px solid #fde68a; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .info-box ul { margin: 8px 0 0; padding-left: 20px; color: #92400e; font-size: 14px; }
        .info-box ul li { margin-bottom: 4px; }
        .footer { background: #f9fafb; padding: 20px 24px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { color: #9ca3af; font-size: 12px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Pembayaran Berhasil!</h1>
            <p>File produk digital kamu sudah siap</p>
        </div>

        <div class="body">
            <p class="greeting">Halo <strong>{{ $buyerName }}</strong>,</p>
            <p style="color:#374151;">Terima kasih sudah melakukan pembelian di <strong>Payou.id</strong>. Pembayaranmu telah dikonfirmasi dan file siap untuk didownload.</p>

            <div class="product-box">
                <h3>📦 Detail Pesanan</h3>
                <p><strong>Produk:</strong> {{ $productName }}</p>
                <p><strong>Order ID:</strong> #{{ $order->order_code }}</p>
                <p><strong>Email:</strong> {{ $order->buyer_email }}</p>
            </div>

            <a href="{{ $downloadUrl }}" class="btn-download">
                ⬇️ Klik di Sini untuk Download File
            </a>

            <div class="info-box">
                <strong>⚠️ Perhatian penting:</strong>
                <ul>
                    <li>Link hanya bisa diakses oleh email: <strong>{{ $order->buyer_email }}</strong></li>
                    <li>Link berlaku hingga: <strong>{{ $expiresAt }}</strong></li>
                    <li>Maksimal download: <strong>{{ $maxDownload }}x</strong></li>
                    <li>Jangan bagikan link ini ke orang lain</li>
                </ul>
            </div>

            <p style="color:#6b7280; font-size:14px;">Jika tombol di atas tidak bisa diklik, copy dan paste link berikut ke browser kamu:</p>
            <p style="word-break:break-all; background:#f3f4f6; padding:12px; border-radius:6px; font-size:13px; color:#374151;">{{ $downloadUrl }}</p>
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis oleh <strong>Payou.id</strong></p>
            <p>Jika ada masalah, hubungi support kami</p>
        </div>
    </div>
</body>
</html>