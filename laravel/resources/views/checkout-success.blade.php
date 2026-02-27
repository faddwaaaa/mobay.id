<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f9fafb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            max-width: 420px;
            width: 100%;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            text-align: center;
        }
        .card-top {
            background: #2563eb;
            padding: 32px 24px 24px;
            color: white;
        }
        .success-icon {
            width: 64px;
            height: 64px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px;
        }
        .card-top h1 { font-size: 22px; margin-bottom: 6px; }
        .card-top p  { font-size: 14px; opacity: 0.85; }
        .card-body { padding: 24px; }
        .detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-row span:last-child { font-weight: 600; color: #111827; }
        .btn {
            display: block;
            margin: 20px auto 0;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover { background: #1d4ed8; }
        .note {
            margin-top: 16px;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-top">
            <div class="success-icon">✅</div>
            <h1>Pembayaran Berhasil!</h1>
            <p>Terima kasih atas pembelian Anda</p>
        </div>
        <div class="card-body">
            @if($transaction)
                <div class="detail-row">
                    <span>No. Order</span>
                    <span>{{ $transaction->order_id }}</span>
                </div>
                <div class="detail-row">
                    <span>Produk</span>
                    <span>{{ $notes['product_title'] ?? '-' }}</span>
                </div>
                <div class="detail-row">
                    <span>Total Bayar</span>
                    <span>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row">
                    <span>Status</span>
                    <span style="color:#16a34a">✔ Lunas</span>
                </div>

                @if(isset($notes['product_type']) && $notes['product_type'] === 'digital')
                    <div class="note">
                        📧 File digital telah dikirim ke <strong>{{ $notes['buyer_email'] ?? '' }}</strong>.<br>
                        Periksa folder inbox atau spam Anda.
                    </div>
                @elseif(isset($notes['product_type']) && $notes['product_type'] === 'umkm')
                    <div class="note">
                        📦 Pesanan Anda sedang diproses. Penjual akan segera menghubungi Anda melalui WhatsApp.
                    </div>
                @endif
            @endif

            <a href="{{ url('/' . ($transaction->user->username ?? '')) }}" class="btn">← Kembali ke Toko</a>
        </div>
    </div>
</body>
</html>
