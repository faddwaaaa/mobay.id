<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Pembayaran</title>
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
            background: #f59e0b;
            padding: 32px 24px 24px;
            color: white;
        }
        .pending-icon {
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
        .card-top p  { font-size: 14px; opacity: 0.9; }
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
        .info-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px;
            font-size: 13px;
            color: #92400e;
            margin-top: 16px;
            text-align: left;
            line-height: 1.6;
        }
        .btn {
            display: block;
            margin: 16px auto 0;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-top">
            <div class="pending-icon">⏳</div>
            <h1>Menunggu Pembayaran</h1>
            <p>Selesaikan pembayaran Anda</p>
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
                <div class="info-box">
                    ⚠️ Harap selesaikan pembayaran sebelum batas waktu yang ditentukan. 
                    Pesanan akan otomatis dibatalkan jika tidak dibayar.
                </div>
            @endif
            <a href="javascript:history.go(-2)" class="btn">← Kembali ke Toko</a>
        </div>
    </div>
</body>
</html>