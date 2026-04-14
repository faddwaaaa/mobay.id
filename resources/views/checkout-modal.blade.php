<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --ink:#0f172a; --muted:#64748b; --line:#e5e7eb; --brand:#2356e8; }
        body {
            font-family: system-ui,-apple-system,sans-serif;
            background: radial-gradient(circle at 10% 10%, #e8efff 0%, #f4f7ff 42%, #eef2ff 100%);
            min-height: 100vh;
            color: var(--ink);
            padding: 20px 14px 34px;
        }
        .wrap { max-width: 920px; margin: 0 auto; }
        .top { display:flex; align-items:center; gap:10px; margin-bottom: 14px; }
        .back {
            width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--line); background:#fff;
            display:flex; align-items:center; justify-content:center; text-decoration:none; color:#475569;
        }
        .ttl { font-size: 19px; font-weight: 800; }
        .hero {
            background: linear-gradient(130deg, #1d4ed8 0%, #2356e8 55%, #3b82f6 100%);
            color: #fff; border-radius: 18px; padding: 20px; margin-bottom: 14px; position: relative; overflow: hidden;
            box-shadow: 0 12px 36px rgba(35,86,232,.25);
        }
        .hero:before {
            content: ""; position: absolute; width: 220px; height: 220px; border-radius: 50%;
            right: -75px; top: -100px; background: rgba(255,255,255,.1);
        }
        .hero h1 { font-size: 22px; margin-bottom: 4px; position: relative; z-index: 1; }
        .hero p { font-size: 13px; opacity: .9; position: relative; z-index: 1; }
        .grid { display:grid; grid-template-columns: 1.15fr .85fr; gap: 14px; }
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; overflow:hidden; box-shadow: 0 8px 24px rgba(15,23,42,.05); }
        .hd { padding: 12px 14px; border-bottom:1px solid #eef2f7; font-size:12px; letter-spacing:.07em; text-transform:uppercase; color:#6b7280; font-weight:800; }
        .bd { padding: 12px 14px; }
        .row { display:flex; justify-content:space-between; gap:12px; padding:8px 0; border-bottom:1px dashed #edf2f7; font-size:13px; }
        .row:last-child { border-bottom:none; }
        .k { color:var(--muted); }
        .v { color:var(--ink); font-weight:700; text-align:right; }
        .note { margin-top:10px; padding:10px 11px; border-radius:10px; background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; font-size:12.5px; line-height:1.5; }
        .action {
            margin-top: 14px; display:flex; gap:10px; flex-wrap:wrap;
        }
        .btn {
            flex:1; min-width:160px; border:none; border-radius:11px; padding:12px 14px; font-size:14px; font-weight:700; cursor:pointer;
            display:inline-flex; align-items:center; justify-content:center; gap:8px; text-decoration:none;
        }
        .btn-back { background:#fff; border:1px solid var(--line); color:#334155; }
        .btn-pay { background:var(--brand); color:#fff; box-shadow:0 8px 20px rgba(37,99,235,.25); }
        .btn-pay:disabled { background:#93c5fd; box-shadow:none; cursor:not-allowed; }
        .spinner { display:none; width:15px; height:15px; border:2px solid rgba(255,255,255,.4); border-top-color:#fff; border-radius:50%; animation:spin .7s linear infinite; }
        .alert { position:fixed; top:16px; right:16px; max-width:320px; padding:11px 12px; border-radius:10px; font-size:13px; font-weight:600; background:#fff1f2; color:#be123c; border:1px solid #fecdd3; box-shadow:0 10px 24px rgba(0,0,0,.14); display:none; z-index:9999; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 860px) { .grid { grid-template-columns:1fr; } .hero h1 { font-size:20px; } }
    </style>
</head>
<body>
@php
    $productPrice = (int) ($product->discount ?: $product->price);
@endphp
<div class="wrap">
    <div class="top">
        <a href="{{ route('checkout.show', $product->id) }}" class="back">←</a>
        <div class="ttl">Pembayaran</div>
    </div>

    <div class="hero">
        <h1>Konfirmasi & Pembayaran</h1>
        <p>Pastikan data Anda benar, lalu pilih metode pembayaran untuk menyelesaikan transaksi.</p>
    </div>

    <div class="grid">
        <div class="card">
            <div class="hd">Detail Pesanan</div>
            <div class="bd">
                <div class="row"><span class="k">Produk</span><span class="v">{{ $product->title }}</span></div>
                <div class="row"><span class="k">Jenis</span><span class="v">{{ strtoupper($product->product_type) }}</span></div>
                <div class="row"><span class="k">Jumlah</span><span class="v">{{ number_format($qty) }}</span></div>
                <div class="row"><span class="k">Harga Satuan</span><span class="v">Rp {{ number_format($productPrice, 0, ',', '.') }}</span></div>
                <div class="row"><span class="k">Subtotal</span><span class="v">Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                @if($shippingEnabled && $shippingCost > 0)
                <div class="row"><span class="k">Ongkir</span><span class="v">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span></div>
                @else
                <div class="row"><span class="k">Ongkir</span><span class="v">Gratis</span></div>
                @endif
                <div class="row"><span class="k">Total Dasar</span><span class="v">Rp {{ number_format($baseTotal, 0, ',', '.') }}</span></div>
                <div class="row"><span class="k">Fee Pembayaran</span><span class="v">Rp {{ number_format($paymentFeeAmount, 0, ',', '.') }}</span></div>
                <div class="row"><span class="k"><strong>TOTAL PEMBAYARAN</strong></span><span class="v"><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></span></div>
                <div class="row"><span class="k">Kurir</span><span class="v">{{ $payload['selected_courier'] ?? '-' }}</span></div>
                <div class="row"><span class="k">Layanan</span><span class="v">{{ $payload['selected_service'] ?? '-' }}</span></div>
            </div>
        </div>

        <div class="card">
            <div class="hd">Data Pembeli</div>
            <div class="bd">
                <div class="row"><span class="k">Nama</span><span class="v">{{ $payload['buyer_name'] ?? '-' }}</span></div>
                <div class="row"><span class="k">Email</span><span class="v">{{ $payload['buyer_email'] ?? '-' }}</span></div>
                <div class="row"><span class="k">WhatsApp</span><span class="v">{{ $payload['buyer_phone'] ?? '-' }}</span></div>
                @if($product->product_type === 'fisik')
                <div class="row"><span class="k">Alamat</span><span class="v">{{ substr($payload['buyer_address'] ?? '', 0, 40) }}...</span></div>
                <div class="row"><span class="k">Kota</span><span class="v">{{ $payload['destination_label'] ?? '-' }}</span></div>
                @endif
            </div>
        </div>
    </div>

    <div class="action">
        <button class="btn btn-back" onclick="history.back()">← Kembali</button>
        <a class="btn btn-pay" href="{{ route('checkout.payment-method.show') }}">Lanjut ke Pembayaran</a>
    </div>

    <div class="note">
        ℹ️ Setelah Anda klik tombol di atas, Anda akan memilih metode pembayaran lalu diarahkan ke halaman <strong>Xendit Hosted Checkout</strong> untuk menyelesaikan transaksi dengan aman.
    </div>
</div>

{{--
// Store product data for payment modal
const PAYMENT_DATA = {
    productId: {{ $product->id }},
    userId: {{ $product->user_id }},
    qty: {{ $qty }},
    orderId: '{{ $orderId ?? '' }}'
};

// Auto-open payment modal on button click
function openPaymentModal() {
    // Set customer info from page
    document.getElementById('xpm-name').value = '{{ $payload["buyer_name"] ?? "" }}';
    document.getElementById('xpm-email').value = '{{ $payload["buyer_email"] ?? "" }}';
    document.getElementById('xpm-phone').value = '{{ $payload["buyer_phone"] ?? "" }}';
    
    // Show modal
    openXenditModal();
}

// Initialize payment modal (from payment-modal.blade.php)
function initXenditModal() {
    const modal = document.getElementById('xendit-payment-modal');
    if (modal) {
        // Modal initialized successfully
        console.log('✓ Payment modal initialized');
    }
}

// On page load
document.addEventListener('DOMContentLoaded', function() {
    initXenditModal();
    // Don't auto-open - wait for button click
});
--}}

</body>
</html>
