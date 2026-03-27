<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg: #f4f7ff;
            --card: #ffffff;
            --line: #e5e7eb;
            --ink: #0f172a;
            --muted: #64748b;
            --brand: #2356e8;
            --ok: #16a34a;
        }
        body {
            font-family: "Plus Jakarta Sans", system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 10% 10%, #e7eeff 0%, var(--bg) 38%, #eef3ff 100%);
            min-height: 100vh;
            color: var(--ink);
            padding: 22px 14px 40px;
        }
        .wrap { max-width: 900px; margin: 0 auto; }
        .hero {
            background: linear-gradient(130deg, #1d4ed8 0%, #2356e8 48%, #3b82f6 100%);
            border-radius: 20px;
            padding: 24px 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(35, 86, 232, .28);
            margin-bottom: 14px;
        }
        .hero::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,.1);
            right: -70px;
            top: -100px;
        }
        .hero h1 { font-size: 24px; font-weight: 800; margin-bottom: 6px; position: relative; z-index: 1; }
        .hero p { font-size: 14px; opacity: .9; position: relative; z-index: 1; }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
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

        /* ── EMAIL STRIP (dalam hero, pisah dengan garis) ── */
        .email-strip {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid rgba(255,255,255,.18);
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
            flex-wrap: wrap;
        }
        .email-strip .icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255,255,255,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .email-strip .icon svg { width: 18px; height: 18px; }
        .email-strip .txt { flex: 1; min-width: 180px; }
        .email-strip .txt span {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 2px;
        }
        .email-strip .txt small {
            font-size: 12px;
            color: rgba(255,255,255,.7);
        }
        .email-strip .txt small b { color: #bfdbfe; }
        .step-pills {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }
        .step-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 11px;
            color: rgba(255,255,255,.8);
            font-weight: 600;
        }
        .step-pill .dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: rgba(255,255,255,.4);
            flex-shrink: 0;
        }
        .step-pill.done .dot { background: #4ade80; }
        .step-pill.active .dot {
            background: #facc15;
            animation: pulse 1.4s ease-in-out infinite;
        }
        .step-arrow { color: rgba(255,255,255,.3); font-size: 10px; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .3; }
        }

        /* ── GRID & CARD ── */
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
        .k { color: #64748b; }
        .v { color: #0f172a; font-weight: 700; text-align: right; }
        .v.ok { color: var(--ok); }
        .note {
            margin-top: 10px;
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 12.5px;
            line-height: 1.5;
        }
        .cta {
            margin-top: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
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
            .email-strip { flex-direction: column; align-items: flex-start; gap: 10px; }
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
    $productType = $notes['product_type'] ?? '';
    $buyerEmail = $notes['buyer_email'] ?? '';
@endphp
<div class="wrap">
    <div class="hero">
        <h1>Pembayaran Berhasil</h1>
        <p>Pesanan Anda sudah kami terima dan sedang diproses.</p>
        <div class="badge">Status: Lunas</div>

        {{-- ── EMAIL STRIP — hanya untuk produk fisik ── --}}
        @if($productType === 'fisik')
        <div class="email-strip">
            <div class="icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="3"/>
                    <path d="M2 7l10 7 10-7"/>
                </svg>
            </div>
            <div class="txt">
                <span>Update pesanan dikirim ke email</span>
                <small>Resi & tracking akan dikirim ke <b>{{ $buyerEmail }}</b></small>
            </div>
            <div class="step-pills">
                <span class="step-pill done"><span class="dot"></span> Konfirmasi</span>
                <span class="step-arrow">›</span>
                <span class="step-pill active"><span class="dot"></span> Diproses</span>
                <span class="step-arrow">›</span>
                <span class="step-pill"><span class="dot"></span> Dikirim</span>
                <span class="step-arrow">›</span>
                <span class="step-pill"><span class="dot"></span> Tiba</span>
            </div>
        </div>
        @endif
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
                <div class="row"><span class="k">Status</span><span class="v ok">Lunas</span></div>
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
                <div class="row"><span class="k">Waktu Transaksi</span><span class="v">{{ optional($transaction->created_at)->format('d M Y H:i') }}</span></div>

                @if($productType === 'digital')
                    <div class="note">File digital dikirim ke email pembeli. Cek inbox/spam untuk memastikan file diterima.</div>
                @elseif($productType === 'fisik')
                    <div class="note">Pesanan fisik akan diproses penjual. Simpan nomor order untuk kebutuhan follow-up.</div>
                @endif

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