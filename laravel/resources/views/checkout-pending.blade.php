<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Pembayaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --line: #e5e7eb;
            --ink: #0f172a;
            --muted: #64748b;
            --warn: #d97706;
            --warn-bg: #fffbeb;
            --brand: #2356e8;
        }
        body {
            font-family: "Plus Jakarta Sans", system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 10% 10%, #fff7e9 0%, #f8fafc 40%, #eef2ff 100%);
            min-height: 100vh;
            color: var(--ink);
            padding: 22px 14px 40px;
        }
        .wrap { max-width: 900px; margin: 0 auto; }
        .hero {
            background: linear-gradient(130deg, #d97706 0%, #f59e0b 55%, #fbbf24 100%);
            border-radius: 20px;
            padding: 24px 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 36px rgba(217, 119, 6, .24);
            margin-bottom: 14px;
        }
        .hero::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,.12);
            right: -75px;
            top: -105px;
        }
        .hero h1 { font-size: 24px; font-weight: 800; margin-bottom: 6px; position: relative; z-index: 1; }
        .hero p { font-size: 14px; opacity: .92; position: relative; z-index: 1; }
        .badge {
            display: inline-flex;
            align-items: center;
            margin-top: 10px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.28);
            font-size: 12px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        .grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 14px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
            overflow: hidden;
        }
        .hd {
            padding: 13px 16px;
            border-bottom: 1px solid #eef2f7;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: #6b7280;
        }
        .bd { padding: 14px 16px; }
        .row {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            font-size: 13px;
            padding: 8px 0;
            border-bottom: 1px dashed #edf2f7;
        }
        .row:last-child { border-bottom: none; }
        .k { color: var(--muted); }
        .v { color: var(--ink); font-weight: 700; text-align: right; }
        .v.warn { color: var(--warn); }
        .info {
            margin-top: 10px;
            background: var(--warn-bg);
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 12.5px;
            color: #92400e;
            line-height: 1.5;
        }
        .cta {
            margin-top: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            background: var(--brand);
            color: #fff;
            text-decoration: none;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 700;
        }
        .cta:hover { background: #1d4ed8; }
        @media (max-width: 860px) {
            .grid { grid-template-columns: 1fr; }
            .hero h1 { font-size: 21px; }
        }
    </style>
</head>
<body>
@php
    $storeUrl = $transaction && $transaction->user ? url('/' . $transaction->user->username) : url('/');
    $qty = (int) ($notes['qty'] ?? 1);
    $unitPrice = (int) ($notes['unit_price'] ?? 0);
    $subtotal = (int) ($notes['subtotal'] ?? ($unitPrice * $qty));
    $shipping = (int) ($notes['shipping_cost'] ?? 0);
@endphp
<div class="wrap">
    <div class="hero">
        <h1>Menunggu Pembayaran</h1>
        <p>Pesanan sudah dibuat. Selesaikan pembayaran untuk melanjutkan proses.</p>
        <div class="badge">Status: Pending</div>
    </div>

    @if($transaction)
    <div class="grid">
        <div class="card">
            <div class="hd">Ringkasan Pesanan</div>
            <div class="bd">
                <div class="row"><span class="k">No. Order</span><span class="v">{{ $transaction->order_id }}</span></div>
                <div class="row"><span class="k">Produk</span><span class="v">{{ $notes['product_title'] ?? '-' }}</span></div>
                <div class="row"><span class="k">Jenis Produk</span><span class="v">{{ strtoupper($notes['product_type'] ?? '-') }}</span></div>
                <div class="row"><span class="k">Qty</span><span class="v">{{ number_format($qty) }}</span></div>
                <div class="row"><span class="k">Harga Satuan</span><span class="v">Rp {{ number_format($unitPrice, 0, ',', '.') }}</span></div>
                <div class="row"><span class="k">Subtotal</span><span class="v">Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                <div class="row"><span class="k">Ongkir</span><span class="v">{{ $shipping > 0 ? 'Rp ' . number_format($shipping, 0, ',', '.') : 'Gratis Ongkir' }}</span></div>
                <div class="row"><span class="k">Total Bayar</span><span class="v">Rp {{ number_format((int) $transaction->amount, 0, ',', '.') }}</span></div>
                <div class="row"><span class="k">Metode Bayar</span><span class="v">{{ strtoupper($transaction->payment_method ?? '-') }}</span></div>
                <div class="row"><span class="k">Status</span><span class="v warn">Belum Dibayar</span></div>
            </div>
        </div>

        <div class="card">
            <div class="hd">Detail Pembeli</div>
            <div class="bd">
                <div class="row"><span class="k">Nama</span><span class="v">{{ $notes['buyer_name'] ?? '-' }}</span></div>
                <div class="row"><span class="k">Email</span><span class="v">{{ $notes['buyer_email'] ?? '-' }}</span></div>
                <div class="row"><span class="k">Telepon</span><span class="v">{{ $notes['buyer_phone'] ?? '-' }}</span></div>
                <div class="row"><span class="k">Area Tujuan</span><span class="v">{{ $notes['destination_label'] ?? '-' }}</span></div>
                <div class="row"><span class="k">Kurir</span><span class="v">{{ $notes['selected_courier'] ?? '-' }}</span></div>
                <div class="row"><span class="k">Layanan</span><span class="v">{{ $notes['selected_service'] ?? '-' }}</span></div>
                <div class="row"><span class="k">Waktu Order</span><span class="v">{{ optional($transaction->created_at)->format('d M Y H:i') }}</span></div>

                <div class="info">
                    Selesaikan pembayaran sebelum masa berlaku habis. Jika tidak dibayar, order akan otomatis dibatalkan sistem.
                </div>

                <a href="{{ $storeUrl }}" class="cta">Kembali ke Toko</a>
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="bd">
            Data transaksi tidak ditemukan.
            <a href="{{ url('/') }}" class="cta" style="max-width:240px;">Kembali ke Beranda</a>
        </div>
    </div>
    @endif
</div>
</body>
</html>

