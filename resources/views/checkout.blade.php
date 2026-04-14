<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $product->title ?? 'Produk' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --ink:#0f172a;
            --muted:#4b5563;
            --line:#d1d5db;
            --brand:#1a4fd8;
            --brand-light:#eff6ff;
            --bg:#f0f4ff;
            --radius:16px;
            --radius-sm:10px;
        }

        body {
            font-family: 'Nunito', system-ui, sans-serif;
            background: linear-gradient(135deg, #e8efff 0%, #f0f4ff 50%, #eef2ff 100%);
            min-height: 100vh;
            padding: 24px 18px 60px;
            color: var(--ink);
            font-size: 17px; /* BASE FONT SIZE LEBIH BESAR */
        }

        /* ====== TOP BAR ====== */
        .top-bar {
            max-width: 900px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .btn-back {
            width: 48px; height: 48px; /* lebih besar */
            border-radius: var(--radius-sm);
            border: 2px solid #d1d5db;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; text-decoration: none; color: #374151;
            transition: all .2s; flex-shrink: 0;
        }
        .btn-back:hover { border-color: var(--brand); color: var(--brand); background: var(--brand-light); }
        .btn-back svg { width: 22px; height: 22px; }
        .top-bar-title { font-size: 22px; font-weight: 900; color: #111827; }

        /* ====== CARD ====== */
        .card {
            max-width: 900px;
            margin: 0 auto 18px;
            background: #fff;
            border: 1.5px solid #e0e7ef;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 10px 32px rgba(15,23,42,.07);
        }
        .card-header {
            padding: 16px 22px;
            border-bottom: 1.5px solid #e5e7eb;
            font-size: 13px; font-weight: 900;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .1em;
            background: #f9fafb;
        }
        .card-body { padding: 22px; }

        /* ====== PRODUCT ROW ====== */
        .product-row { display: flex; gap: 18px; align-items: flex-start; }
        .product-img {
            width: 100px; height: 100px;
            border-radius: 12px; object-fit: cover;
            flex-shrink: 0; border: 1.5px solid #e5e7eb;
        }
        .product-img-ph {
            width: 100px; height: 100px;
            border-radius: 12px; background: #eff6ff;
            flex-shrink: 0; display: flex;
            align-items: center; justify-content: center;
            font-size: 36px; border: 1.5px solid #e5e7eb;
        }
        .product-meta { flex: 1; min-width: 0; }
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 13px; font-weight: 800;
            padding: 5px 12px; border-radius: 20px; margin-bottom: 8px;
        }
        .badge.fisik  { background: #f0fdf4; color: #15803d; }
        .badge.digital { background: #eff6ff; color: #1d4ed8; }
        .product-meta h2 { font-size: 18px; font-weight: 800; color: #111827; margin-bottom: 8px; line-height: 1.4; }
        .price-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .price-final { font-size: 22px; font-weight: 900; color: var(--brand); }
        .price-ori  { font-size: 15px; color: #9ca3af; text-decoration: line-through; }
        .disc-badge { background: #fee2e2; color: #dc2626; font-size: 13px; font-weight: 800; padding: 3px 8px; border-radius: 6px; }

        .seller-row {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 0 0; margin-top: 14px;
            border-top: 1.5px solid #f3f4f6;
        }
        .seller-ava    { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
        .seller-ava-ph { width: 40px; height: 40px; border-radius: 50%; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .seller-row .seller-text { font-size: 15px; color: #374151; }

        .meta-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 10px; margin-top: 14px; }
        .meta-box { border: 1.5px solid #edf2f7; background: #f8fafc; border-radius: 12px; padding: 11px 14px; }
        .meta-box .mk { font-size: 11px; color: #6b7280; text-transform: uppercase; font-weight: 800; letter-spacing: .06em; margin-bottom: 4px; }
        .meta-box .mv { font-size: 14px; color: #0f172a; font-weight: 800; }

        /* ====== FORM ====== */
        .form-group { margin-bottom: 20px; }  /* lebih besar */

        label {
            display: block;
            font-size: 16px; /* lebih besar */
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .req { color: #ef4444; margin-left: 3px; }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 14px 16px; /* lebih tinggi */
            border: 2px solid #d1d5db; /* border lebih tebal */
            border-radius: 12px;
            font-size: 16px; /* lebih besar */
            color: #111827;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
            font-family: 'Nunito', inherit;
            line-height: 1.5;
        }
        input:focus, textarea:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(26,79,216,.10);
        }
        textarea { resize: vertical; min-height: 96px; }

        .hint {
            font-size: 14px; /* lebih besar */
            color: #6b7280;
            margin-top: 6px;
            line-height: 1.5;
        }

        /* ====== QTY ====== */
        .qty-wrap {
            display: flex; align-items: center;
            justify-content: space-between;
            background: #f9fafb;
            border: 2px solid #d1d5db;
            border-radius: 14px;
            padding: 10px 14px;
            gap: 12px;
        }
        .qty-info { font-size: 15px; color: #4b5563; font-weight: 600; }
        .qty-ctrl {
            display: flex; align-items: center;
            background: #fff; border: 2px solid #d1d5db;
            border-radius: 12px; overflow: hidden;
        }
        .qty-btn {
            width: 48px; height: 48px; /* jauh lebih besar */
            border: none; background: transparent;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #374151; font-size: 20px; font-weight: 700;
            transition: all .15s; flex-shrink: 0; user-select: none;
        }
        .qty-btn:hover:not(:disabled) { background: #eff6ff; color: var(--brand); }
        .qty-btn:disabled { color: #d1d5db; cursor: not-allowed; }
        .qty-btn svg { width: 18px; height: 18px; }
        .qty-input {
            min-width: 64px; width: 64px;
            text-align: center;
            font-size: 18px; font-weight: 900;
            color: #111827;
            border: none !important;
            border-left: 2px solid #d1d5db !important;
            border-right: 2px solid #d1d5db !important;
            border-radius: 0 !important;
            padding: 0 6px !important;
            height: 48px;
            background: #fff;
            box-shadow: none !important;
            outline: none;
            -moz-appearance: textfield;
            font-family: 'Nunito', inherit;
        }
        .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; }

        /* ====== AUTOCOMPLETE ====== */
        .city-wrap { position: relative; }
        .city-dd {
            position: absolute; top: calc(100% + 6px); left: 0; right: 0;
            background: #fff; border: 2px solid #d1d5db;
            border-radius: 14px;
            box-shadow: 0 12px 32px rgba(0,0,0,.12);
            z-index: 100; max-height: 280px; overflow-y: auto; display: none;
        }
        .city-dd.show { display: block; }
        .city-item {
            padding: 14px 18px; /* lebih besar */
            font-size: 15px; color: #374151;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            line-height: 1.5;
        }
        .city-item:last-child { border-bottom: none; }
        .city-item:hover, .city-item.active { background: #eff6ff; color: var(--brand); }
        .city-item .city-main { font-weight: 700; font-size: 15px; }
        .city-item .city-sub  { font-size: 13px; color: #6b7280; }
        .city-badge {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 8px; padding: 7px 14px;
            background: #f0fdf4; border: 1.5px solid #bbf7d0;
            border-radius: 8px; font-size: 14px; color: #15803d; font-weight: 700;
        }
        .address-wrap { position: relative; }
        .address-helper {
            margin-top: 8px;
            padding: 10px 12px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
        }

        /* ====== ONGKIR ====== */
        .ongkir-section { margin-top: 18px; }
        .ongkir-loading {
            display: flex; align-items: center; gap: 10px;
            padding: 16px; background: #f9fafb;
            border: 2px solid #d1d5db; border-radius: 12px;
            font-size: 15px; color: #6b7280;
        }
        .ongkir-loading svg { animation: spin 1s linear infinite; flex-shrink: 0; }
        .ongkir-error {
            padding: 14px 16px;
            background: #fef2f2; border: 2px solid #fecaca;
            border-radius: 12px; font-size: 14px; color: #dc2626;
        }
        .ongkir-list { display: flex; flex-direction: column; gap: 10px; }
        .ongkir-item {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 16px; /* lebih besar */
            border: 2px solid #d1d5db;
            border-radius: 14px; cursor: pointer;
            transition: all .15s; background: #fff;
        }
        .ongkir-item:hover    { border-color: var(--brand); background: #f8faff; }
        .ongkir-item.selected { border-color: var(--brand); background: #eff6ff; }
        .ongkir-item input[type="radio"] { accent-color: var(--brand); width: 20px; height: 20px; flex-shrink: 0; }
        .ongkir-badge { font-size: 12px; font-weight: 800; padding: 4px 9px; border-radius: 6px; background: #e0e7ff; color: #4338ca; flex-shrink: 0; }
        .ongkir-name  { font-size: 15px; font-weight: 700; color: #111827; flex: 1; }
        .ongkir-etd   { font-size: 13px; color: #6b7280; }
        .ongkir-price { font-size: 16px; font-weight: 900; color: var(--brand); }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ====== SUMMARY ====== */
        .sum-row {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 16px; /* lebih besar */
            color: #374151; padding: 9px 0;
            border-bottom: 1px dashed #e5e7eb;
        }
        .sum-row:last-of-type { border-bottom: none; }
        .sum-row.total {
            font-weight: 900; font-size: 19px; color: #111827;
            padding-top: 16px; margin-top: 8px;
            border-top: 2px solid #d1d5db;
            border-bottom: none;
        }
        .sum-row.total .total-amt { color: var(--brand); font-size: 22px; }
        .sum-ph { color: #9ca3af; font-style: italic; font-size: 14px; }

        /* ====== TOMBOL BAYAR ====== */
        .btn-pay {
            width: 100%; max-width: 900px;
            margin: 0 auto; display: block;
            background: var(--brand); color: #fff;
            border: none; border-radius: var(--radius);
            padding: 20px; /* jauh lebih besar */
            font-size: 18px; font-weight: 900; /* lebih besar */
            cursor: pointer;
            transition: background .2s, transform .1s;
            box-shadow: 0 10px 28px rgba(26,79,216,.30);
            font-family: 'Nunito', inherit;
            letter-spacing: .02em;
        }
        .btn-pay:hover:not(:disabled) { background: #1440c0; transform: translateY(-1px); }
        .btn-pay:active:not(:disabled) { transform: translateY(0); }
        .btn-pay:disabled { background: #93c5fd; cursor: not-allowed; box-shadow: none; }
        .spinner {
            display: none; width: 20px; height: 20px;
            border: 3px solid rgba(255,255,255,.35);
            border-top-color: #fff; border-radius: 50%;
            animation: spin .7s linear infinite;
            margin-right: 10px; vertical-align: middle;
        }

        /* ====== ALERT INFO ====== */
        .alert-info {
            max-width: 900px; margin: 0 auto 18px;
            padding: 16px 18px; border-radius: 14px;
            font-size: 15px;
            display: flex; align-items: flex-start; gap: 10px;
            background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1d4ed8;
            font-weight: 600;
        }

        /* ====== TOAST ALERT ====== */
        .checkout-alert {
            position: fixed; top: 20px; right: 20px; z-index: 9999;
            min-width: 260px; max-width: 380px;
            padding: 16px 18px; border-radius: 14px;
            font-size: 15px; font-weight: 700; line-height: 1.5;
            box-shadow: 0 12px 32px rgba(0,0,0,.18);
            opacity: 0; transform: translateY(-10px);
            transition: all .2s ease; pointer-events: none;
        }
        .checkout-alert.show  { opacity: 1; transform: translateY(0); }
        .checkout-alert.error   { background: #fff1f2; border: 1.5px solid #fecdd3; color: #be123c; }
        .checkout-alert.success { background: #ecfdf5; border: 1.5px solid #bbf7d0; color: #166534; }

        /* ====== SECURE FOOTER ====== */
        .secure {
            max-width: 900px; margin: 16px auto 0;
            text-align: center; font-size: 14px; color: #9ca3af;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }

        /* ====== DIVIDER BETWEEN FIELDS ====== */
        .field-divider { height: 1px; background: #f0f0f0; margin: 4px 0 20px; }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 700px) {
            body { font-size: 16px; padding: 16px 14px 50px; }
            .meta-grid { grid-template-columns: 1fr; }
            .top-bar-title { font-size: 20px; }
            .btn-pay { font-size: 17px; padding: 18px; }
            .price-final { font-size: 20px; }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <a href="javascript:history.back()" class="btn-back">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div class="top-bar-title">🛒 Checkout</div>
</div>

@if($product->product_type === 'digital')
<div class="alert-info">
    <span style="font-size:20px">📦</span>
    <span>Produk digital — file akan dikirim ke <strong>email Anda</strong> setelah pembayaran berhasil.</span>
</div>
@endif

{{-- DETAIL PRODUK --}}
<div class="card">
    <div class="card-header">📋 Detail Produk</div>
    <div class="card-body">
        <div class="product-row">
            @if($product->images && $product->images->count())
                <img src="{{ asset('storage/'.$product->images->first()->image) }}" class="product-img" alt="{{ $product->title }}">
            @else
                <div class="product-img-ph">🛍️</div>
            @endif
            <div class="product-meta">
                <span class="badge {{ $product->product_type }}">
                    {{ $product->product_type === 'digital' ? '💾 Digital' : '📦 Fisik' }}
                </span>
                <h2>{{ $product->title }}</h2>
                <div class="price-row">
                    <span class="price-final">Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}</span>
                    @if($product->discount && $product->discount < $product->price)
                        <span class="price-ori">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <span class="disc-badge">-{{ round((($product->price - $product->discount) / $product->price) * 100) }}%</span>
                    @endif
                </div>
                @if($product->product_type === 'fisik' && $product->weight)
                    <div style="margin-top:6px;font-size:13px;color:#6b7280;font-weight:600;">⚖️ Berat: {{ $product->weight }} gram</div>
                @endif
            </div>
        </div>
        @if($seller)
        <div class="seller-row">
            @if($seller->avatar ?? null)
                <img src="{{ $seller->avatar_url }}" class="seller-ava" alt="{{ $seller->name }}">
            @else
                <div class="seller-ava-ph">👤</div>
            @endif
            <div class="seller-text">
                Dijual oleh <strong>{{ $seller->name }}</strong>
                @if($seller->origin_city_name ?? null)
                    <span style="font-size:13px;color:#6b7280;"> · 📍 {{ $seller->origin_city_name }}</span>
                @endif
            </div>
        </div>
        @endif
        <div class="meta-grid">
            <div class="meta-box">
                <div class="mk">SKU Produk</div>
                <div class="mv">#{{ $product->id }}</div>
            </div>
            <div class="meta-box">
                <div class="mk">Pengiriman</div>
                <div class="mv">
                    @if($product->product_type === 'fisik')
                        {{ ($product->shipping_enabled ?? true) ? 'Ongkir Otomatis' : 'Gratis Ongkir' }}
                    @else
                        Digital Delivery
                    @endif
                </div>
            </div>
            <div class="meta-box">
                <div class="mk">Metode Bayar</div>
                <div class="mv">Midtrans (Snap)</div>
            </div>
        </div>
    </div>
</div>

<form id="checkoutForm" autocomplete="off" method="POST" action="{{ route('checkout.checkpoint.store') }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">

    {{-- DATA PEMBELI --}}
    <div class="card">
        <div class="card-header">👤 Data Pembeli</div>
        <div class="card-body">

            <div class="form-group">
                <label>Nama Lengkap <span class="req">*</span></label>
                <input type="text" name="buyer_name"
                       value="{{ auth()->user()->name ?? '' }}"
                       placeholder="Contoh: Budi Santoso"
                       required>
            </div>

            <div class="form-group">
                <label>Alamat Email <span class="req">*</span></label>
                <input type="email" name="buyer_email"
                       value="{{ auth()->user()->email ?? '' }}"
                       placeholder="Contoh: budi@email.com"
                       required>
                @if($product->product_type === 'digital')
                    <div class="hint">📧 File digital akan dikirim ke alamat email ini.</div>
                @endif
            </div>

            <div class="form-group">
                <label>Nomor WhatsApp <span class="req">*</span></label>
                <input type="tel" name="buyer_phone"
                       placeholder="Contoh: 08123456789"
                       required>
                <div class="hint">Digunakan untuk konfirmasi pesanan.</div>
            </div>

            @if($product->product_type === 'fisik')

            @if(($product->shipping_enabled ?? true))
            {{-- AREA TUJUAN --}}
            <div class="form-group">
                <label>Kelurahan / Kecamatan Tujuan <span class="req">*</span></label>
                <div class="city-wrap">
                    <input type="text" id="citySearch"
                           placeholder="Ketik nama area, contoh: Purwokerto"
                           autocomplete="off">
                    <div class="city-dd" id="cityDropdown"></div>
                </div>
                <div id="cityBadge" style="display:none" class="city-badge">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="cityBadgeText"></span>
                </div>
                <div class="hint">💡 Ketik minimal 3 huruf untuk mencari area.</div>
                <input type="hidden" name="destination_village_code" id="destVillageCode">
                <input type="hidden" name="destination_label" id="destLabel">
            </div>

            <div class="form-group">
                <label>Alamat Lengkap <span class="req">*</span></label>
                <div class="address-wrap">
                    <textarea name="buyer_address"
                              id="buyerAddress"
                              placeholder="Nama jalan, nomor rumah, RT/RW, lalu ketik kelurahan/kecamatan di bagian akhir untuk melihat rekomendasi..."
                              required></textarea>
                    <div class="city-dd" id="addressDropdown"></div>
                </div>
                <div class="address-helper">
                    Ketik detail alamat dulu, lalu isi nama kelurahan atau kecamatan di bagian akhir untuk memilih rekomendasi area.
                </div>
            </div>

            <div class="form-group">
                <label>Catatan untuk Penjual</label>
                <input type="text" name="buyer_notes"
                       placeholder="Contoh: warna merah, ukuran L (tidak wajib diisi)">
            </div>

            @php $maxQty = $product->purchase_limit ?? ($product->stock ?? 99); @endphp
            <div class="form-group">
                <label>Jumlah Pesanan <span class="req">*</span></label>
                <div class="qty-wrap">
                    <div class="qty-info">
                        @if($product->purchase_limit) Maks. {{ $product->purchase_limit }} per transaksi
                        @elseif($product->stock) Stok tersedia: {{ $product->stock }}
                        @else Pilih jumlah @endif
                    </div>
                    <div class="qty-ctrl">
                        <button type="button" class="qty-btn" id="qtyMinus" onclick="changeQty(-1)" disabled>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 12h14"/></svg>
                        </button>
                        <input type="number" id="qtyDisplay" class="qty-input" value="1" min="1" max="{{ $maxQty }}" inputmode="numeric">
                        <button type="button" class="qty-btn" id="qtyPlus" onclick="changeQty(1)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>
                </div>
                <input type="number" name="qty" id="qtyHidden" value="1" min="1" max="{{ $maxQty }}" style="display:none">
            </div>

            {{-- PILIH ONGKIR --}}
            <div class="form-group ongkir-section" id="ongkirSection" style="display:none">
                <label>Layanan Pengiriman <span class="req">*</span></label>
                <div id="ongkirContent"></div>
                <input type="hidden" name="selected_courier" id="selCourier" value="">
                <input type="hidden" name="selected_service" id="selService" value="">
                <input type="hidden" name="selected_ongkir_cost" id="selCost" value="0">
            </div>

            @else
            {{-- SHIPPING DISABLED --}}
            <div class="form-group">
                <label>Alamat Lengkap <span class="req">*</span></label>
                <textarea name="buyer_address"
                          placeholder="Nama jalan, nomor rumah, RT/RW, desa/kelurahan..."
                          required></textarea>
            </div>
            <div class="form-group">
                <label>Catatan untuk Penjual</label>
                <input type="text" name="buyer_notes"
                       placeholder="Contoh: warna merah, ukuran L (tidak wajib diisi)">
            </div>
            @php $maxQty = $product->purchase_limit ?? ($product->stock ?? 99); @endphp
            <div class="form-group">
                <label>Jumlah Pesanan <span class="req">*</span></label>
                <div class="qty-wrap">
                    <div class="qty-info">
                        @if($product->purchase_limit) Maks. {{ $product->purchase_limit }} per transaksi
                        @elseif($product->stock) Stok tersedia: {{ $product->stock }}
                        @else Pilih jumlah @endif
                    </div>
                    <div class="qty-ctrl">
                        <button type="button" class="qty-btn" id="qtyMinus" onclick="changeQty(-1)" disabled>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 12h14"/></svg>
                        </button>
                        <input type="number" id="qtyDisplay" class="qty-input" value="1" min="1" max="{{ $maxQty }}" inputmode="numeric">
                        <button type="button" class="qty-btn" id="qtyPlus" onclick="changeQty(1)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>
                </div>
                <input type="number" name="qty" id="qtyHidden" value="1" min="1" max="{{ $maxQty }}" style="display:none">
            </div>
            <input type="hidden" name="selected_courier" id="selCourier" value="FREE">
            <input type="hidden" name="selected_service" id="selService" value="Tanpa Ongkir">
            <input type="hidden" name="selected_ongkir_cost" id="selCost" value="0">
            <div class="form-group">
                <div class="hint" style="margin-top:0;color:#15803d;font-weight:700;font-size:15px;">
                    ✅ Produk ini menggunakan Gratis Ongkir.
                </div>
            </div>
            @endif

            @else
                <input type="hidden" name="qty" value="1">
            @endif
        </div>
    </div>

    <input type="hidden" name="payment_method" value="gopay">

    {{-- RINGKASAN PEMBAYARAN --}}
    <div class="card">
        <div class="card-header">💳 Ringkasan Pembayaran</div>
        <div class="card-body">
            <div class="sum-row">
                <span>Harga satuan</span>
                <span style="font-weight:700;">Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}</span>
            </div>
            @if($product->product_type === 'fisik')
            <div class="sum-row">
                <span>Jumlah</span>
                <span style="font-weight:700;" id="sumQty">1</span>
            </div>
            <div class="sum-row">
                <span>Subtotal</span>
                <span style="font-weight:700;" id="sumSubtotal">Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}</span>
            </div>
            <div class="sum-row">
                <span>Ongkos Kirim</span>
                <span id="sumShipping">
                    @if(($product->shipping_enabled ?? true))
                        <span class="sum-ph">Pilih area tujuan dulu</span>
                    @else
                        <span style="color:#15803d;font-weight:700;">Gratis Ongkir</span>
                    @endif
                </span>
            </div>
            @endif
            <div class="sum-row" style="color:#6b7280;">
                <span style="display:flex;align-items:center;gap:8px;">
                    Biaya Admin & Gateway
                    <span style="font-size:12px;padding:3px 8px;background:#fef3c7;color:#92400e;
                                 border-radius:5px;font-weight:800;">Tergantung metode bayar</span>
                </span>
                <span id="sumFee" style="color:#b45309;font-weight:700;">Dihitung setelah pilih metode</span>
            </div>
            <div class="sum-row total">
                <span>Total Sementara</span>
                <span class="total-amt" id="sumTotal">Rp {{ number_format((int)($product->discount ?? $product->price), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <button type="submit" class="btn-pay" id="btnPay"
        @if($product->product_type==='fisik' && ($product->shipping_enabled ?? true)) disabled @endif>
        <span class="spinner" id="paySpinner"></span>
        <span id="btnPayText">
            {{ $product->product_type === 'fisik' && ($product->shipping_enabled ?? true)
                ? '⬆ Pilih Ongkir Dulu'
                : '✅ Lanjut ke Review Pembayaran' }}
        </span>
    </button>
</form>

<div class="secure">🔒 Pembayaran aman & terenkripsi via Midtrans</div>
<div id="checkoutAlert" class="checkout-alert error"></div>

<script>
const UNIT_PRICE       = {{ $product->discount ?? $product->price ?? 0 }};
const MAX_QTY          = {{ $product->purchase_limit ?? ($product->stock ?? 99) }};
const PRODUCT_TYPE     = '{{ $product->product_type }}';
const SHIPPING_ENABLED = {{ ($product->product_type === 'fisik' && ($product->shipping_enabled ?? true)) ? 'true' : 'false' }};
const ORIGIN_AREA_ID   = '{{ $seller->origin_village_code ?? '' }}';
const WEIGHT_GRAM      = {{ (int)($product->weight ?? 1000) }};

let currentQty      = 1;
let selectedCost    = 0;
let destinationArea = null;
let isLoadingOngkir = false;

function fmtRp(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n); }

// ===== SUMMARY =====
function updateSummary(qty) {
    qty = Math.min(Math.max(qty, 1), MAX_QTY);
    const sub  = UNIT_PRICE * qty;
    const ship = PRODUCT_TYPE === 'fisik' ? (SHIPPING_ENABLED ? selectedCost : 0) : 0;
    const base = sub + ship;

    const elQ    = document.getElementById('sumQty');
    const elS    = document.getElementById('sumSubtotal');
    const elShip = document.getElementById('sumShipping');
    const elFee  = document.getElementById('sumFee');
    const elT    = document.getElementById('sumTotal');

    if (elQ) elQ.textContent = qty;
    if (elS) elS.textContent = fmtRp(sub);
    if (elShip && PRODUCT_TYPE === 'fisik') {
        elShip.innerHTML = SHIPPING_ENABLED
            ? (ship > 0 ? `<span style="font-weight:700">${fmtRp(ship)}</span>` : '<span class="sum-ph">Belum dipilih</span>')
            : '<span style="color:#15803d;font-weight:700;">Gratis Ongkir</span>';
    }
    if (elFee) elFee.textContent = 'Dihitung setelah pilih metode';
    if (elT)   elT.textContent   = fmtRp(base);
}

// ===== QTY =====
const qtyDisplay = document.getElementById('qtyDisplay');
const qtyHidden  = document.getElementById('qtyHidden');
const qtyMinus   = document.getElementById('qtyMinus');
const qtyPlus    = document.getElementById('qtyPlus');

function applyQty(n, options = {}) {
    const { reloadOngkir = true } = options;
    if (isNaN(n) || n < 1) n = 1;
    if (n > MAX_QTY) n = MAX_QTY;
    currentQty = n;
    if (qtyDisplay) qtyDisplay.value = n;
    if (qtyHidden)  qtyHidden.value  = n;
    if (qtyMinus)   qtyMinus.disabled = n <= 1;
    if (qtyPlus)    qtyPlus.disabled  = n >= MAX_QTY;
    updateSummary(n);
    if (reloadOngkir && PRODUCT_TYPE === 'fisik' && SHIPPING_ENABLED && destinationArea && ORIGIN_AREA_ID) {
        loadOngkir(ORIGIN_AREA_ID, destinationArea.village_code, WEIGHT_GRAM * n);
    }
}
function changeQty(d) { applyQty(currentQty + d); }
if (qtyDisplay) {
    qtyDisplay.addEventListener('input', function () { const v = parseInt(this.value, 10); if (!isNaN(v)) applyQty(v); });
    qtyDisplay.addEventListener('blur',  function () { applyQty(parseInt(this.value, 10)); });
    qtyDisplay.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); this.blur(); }
        if (!['Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Tab','Home','End'].includes(e.key) && !/^[0-9]$/.test(e.key)) e.preventDefault();
    });
}

// ===== AUTOCOMPLETE AREA =====
const citySearch    = document.getElementById('citySearch');
const cityDropdown  = document.getElementById('cityDropdown');
const cityBadge     = document.getElementById('cityBadge');
const cityBadgeText = document.getElementById('cityBadgeText');
const destVillageCodeEl = document.getElementById('destVillageCode');
const destLabelEl       = document.getElementById('destLabel');
const buyerAddressEl    = document.getElementById('buyerAddress');
const addressDropdown   = document.getElementById('addressDropdown');
let cityTimer = null;
let addressTimer = null;
const citySearchCache = {};
const citySearchKeys = [];
const addressSearchCache = {};
const addressSearchKeys = [];
let citySearchToken = 0;
let addressSearchToken = 0;

function normalizeQuery(q) {
    return String(q || '').replace(/\s+/g, ' ').trim().toLowerCase();
}

function filterAreaData(data, q) {
    if (!Array.isArray(data) || !data.length) return [];
    const needle = normalizeQuery(q);
    if (!needle) return [];
    return data.filter((v) => {
        const village = String(v.village_name || '').toLowerCase();
        const district = String(v.district_name || '').toLowerCase();
        const city = String(v.city_name || '').toLowerCase();
        return village.includes(needle) || district.includes(needle) || city.includes(needle);
    });
}

function getCachedAreaResults(cache, keys, q) {
    const needle = normalizeQuery(q);
    if (!needle || needle.length < 2) return [];
    if (cache[needle]) return cache[needle];

    let bestFallback = null;
    for (const key of keys) {
        const item = cache[key];
        if (!item || !item.length) continue;

        const commonPrefixLength = (function(a, b) {
            let i = 0;
            while (i < a.length && i < b.length && a[i] === b[i]) i++;
            return i;
        })(key, needle);

        const isClose = needle.startsWith(key)
            || key.startsWith(needle)
            || key.includes(needle)
            || needle.includes(key)
            || commonPrefixLength >= 4;

        if (!isClose) continue;

        const filtered = filterAreaData(item, needle);
        if (filtered.length) return filtered;

        if (!bestFallback) {
            bestFallback = item;
        }
    }

    if (bestFallback) {
        return bestFallback.slice(0, 20);
    }

    // final generic fallback: scan all cache entries
    const final = [];
    const seen = new Set();
    for (const entry of Object.values(cache)) {
        if (!Array.isArray(entry)) continue;
        const f = filterAreaData(entry, needle);
        for (const v of f) {
            if (seen.has(v.village_code)) continue;
            seen.add(v.village_code);
            final.push(v);
            if (final.length >= 20) break;
        }
        if (final.length >= 20) break;
    }
    return final;
}

if (citySearch) {
    citySearch.addEventListener('input', function () {
        const q = this.value.replace(/\s+/g, ' ').trim();
        destinationArea = null; selectedCost = 0;
        if (destVillageCodeEl) destVillageCodeEl.value = '';
        if (destLabelEl) destLabelEl.value = '';
        if (cityBadge) cityBadge.style.display = 'none';
        if (SHIPPING_ENABLED) clearCourierSelection();
        updateSummary(currentQty);
        clearTimeout(cityTimer);

        if (!SHIPPING_ENABLED || q.length < 2) { cityDropdown.classList.remove('show'); return; }

        const k = normalizeQuery(q);
        cityDropdown.innerHTML = '<div class="city-item" style="color:#9ca3af">🔍 Mencari area...</div>';
        cityDropdown.classList.add('show');

        const cached = getCachedAreaResults(citySearchCache, citySearchKeys, q);
        renderCityResults(cached);

        const requestToken = ++citySearchToken;
        cityTimer = setTimeout(() => searchCity(q, requestToken), 150);
    });
    document.addEventListener('click', (e) => {
        if (!citySearch.contains(e.target) && !cityDropdown.contains(e.target))
            cityDropdown.classList.remove('show');
    });
}

if (buyerAddressEl && addressDropdown) {
    buyerAddressEl.addEventListener('input', function () {
        const q = extractAddressSearchTerm(this.value);
        clearTimeout(addressTimer);
        if (q.length < 2) {
            addressDropdown.classList.remove('show');
            return;
        }
        addressDropdown.innerHTML = '<div class="city-item" style="color:#9ca3af">Mencari rekomendasi area...</div>';
        addressDropdown.classList.add('show');

        const cached = getCachedAreaResults(addressSearchCache, addressSearchKeys, q);
        renderAddressResults(cached);

        const requestToken = ++addressSearchToken;
        addressTimer = setTimeout(() => searchAddressSuggestion(q, requestToken), 150);
    });

    document.addEventListener('click', (e) => {
        if (!buyerAddressEl.contains(e.target) && !addressDropdown.contains(e.target)) {
            addressDropdown.classList.remove('show');
        }
    });
}

function renderCityResults(data) {
    cityDropdown.innerHTML = '';
    if (!Array.isArray(data) || !data.length) {
        cityDropdown.innerHTML = '<div class="city-item" style="color:#9ca3af">Sedang mencari lokasi…</div>';
        return;
    }
    data.forEach((v) => {
        const el = document.createElement('div');
        el.className = 'city-item';
        el.innerHTML = `<div class="city-main">${v.village_name || '-'}</div>
                        <div class="city-sub">${v.district_name || ''}, ${v.city_name || ''}, ${v.province || ''}</div>`;
        el.addEventListener('click', () => selectCity(v));
        cityDropdown.appendChild(el);
    });
}

async function searchCity(q, token) {
    const currentToken = token;
    try {
        const res  = await fetch('/api/ongkir/cities?q=' + encodeURIComponent(q));
        const data = await res.json();

        if (currentToken !== citySearchToken) return;

        const key = normalizeQuery(q);
        citySearchCache[key] = data;
        if (!citySearchKeys.includes(key)) {
            citySearchKeys.unshift(key);
            if (citySearchKeys.length > 35) citySearchKeys.pop();
        }

        const filtered = filterAreaData(data, q);
        renderCityResults(filtered.length ? filtered : data);
    } catch (e) {
        if (currentToken !== citySearchToken) return;
        cityDropdown.innerHTML = '<div class="city-item" style="color:#dc2626">Gagal memuat area</div>';
    }
}

function selectCity(v) {
    const label = `${v.village_name || ''}, ${v.district_name || ''}, ${v.city_name || ''}`.replace(/\s+,/g,',').replace(/^,\s*/,'');
    citySearch.value = label;
    destinationArea  = v;
    if (destVillageCodeEl) destVillageCodeEl.value = v.village_code || '';
    if (destLabelEl)       destLabelEl.value = label;
    if (cityBadgeText)     cityBadgeText.textContent = `${label}${v.province ? ', '+v.province : ''}`;
    if (cityBadge)         cityBadge.style.display = 'inline-flex';
    cityDropdown.classList.remove('show');
    if (!SHIPPING_ENABLED) return;
    if (!ORIGIN_AREA_ID) { showOngkirError('Penjual belum mengatur area asal pengiriman.'); return; }
    loadOngkir(ORIGIN_AREA_ID, v.village_code, WEIGHT_GRAM * currentQty);
}

function extractAddressSearchTerm(value) {
    return String(value || '')
        .split(/\n|,/)
        .map(part => part.trim())
        .filter(Boolean)
        .pop() || '';
}

function renderAddressResults(data) {
    addressDropdown.innerHTML = '';
    if (!Array.isArray(data) || !data.length) {
        addressDropdown.innerHTML = '<div class="city-item" style="color:#9ca3af">Sedang mencari rekomendasi area…</div>';
        return;
    }
    data.forEach((v) => {
        const el = document.createElement('div');
        el.className = 'city-item';
        el.innerHTML = `<div class="city-main">${v.village_name || '-'}</div>
                        <div class="city-sub">${v.district_name || ''}, ${v.city_name || ''}, ${v.province || ''}</div>`;
        el.addEventListener('click', () => applyAddressSuggestion(v));
        addressDropdown.appendChild(el);
    });
}

async function searchAddressSuggestion(q, token) {
    const currentToken = token;
    try {
        const res = await fetch('/api/ongkir/cities?q=' + encodeURIComponent(q));
        const data = await res.json();

        if (currentToken !== addressSearchToken) return;

        const key = normalizeQuery(q);
        addressSearchCache[key] = data;
        if (!addressSearchKeys.includes(key)) {
            addressSearchKeys.unshift(key);
            if (addressSearchKeys.length > 35) addressSearchKeys.pop();
        }

        const filtered = filterAreaData(data, q);
        renderAddressResults(filtered.length ? filtered : data);
    } catch (e) {
        if (currentToken !== addressSearchToken) return;
        addressDropdown.innerHTML = '<div class="city-item" style="color:#dc2626">Gagal memuat rekomendasi area</div>';
    }
}

function applyAddressSuggestion(v) {
    if (!buyerAddressEl) return;
    const areaLabel = `${v.village_name || ''}, ${v.district_name || ''}, ${v.city_name || ''}`
        .replace(/\s+,/g, ',')
        .replace(/^,\s*/, '')
        .trim();
    const current = buyerAddressEl.value.replace(/\s+$/g, '');
    const lastSeparator = Math.max(current.lastIndexOf(','), current.lastIndexOf('\n'));
    let nextValue = areaLabel;

    if (current) {
        if (current.includes(areaLabel)) {
            nextValue = current;
        } else if (lastSeparator >= 0) {
            const prefix = current.slice(0, lastSeparator).trim().replace(/[,\s]+$/g, '');
            nextValue = prefix ? `${prefix}, ${areaLabel}` : areaLabel;
        } else {
            nextValue = `${current}, ${areaLabel}`;
        }
    }

    buyerAddressEl.value = nextValue;
    addressDropdown.classList.remove('show');
    if (SHIPPING_ENABLED && citySearch && typeof selectCity === 'function') {
        selectCity(v);
    }
    buyerAddressEl.focus();
    buyerAddressEl.setSelectionRange(nextValue.length, nextValue.length);
}

async function loadOngkir(originAreaId, destinationAreaId, weightGram) {
    const section = document.getElementById('ongkirSection');
    const content = document.getElementById('ongkirContent');
    if (!section || !content) return;
    section.style.display = 'block';
    content.innerHTML = '<div class="ongkir-loading"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="4" style="opacity:.3"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:.8"/></svg>Menghitung ongkir, mohon tunggu...</div>';
    isLoadingOngkir = true;
    clearCourierSelection();
    updateSummary(currentQty);
    try {
        const res = await fetch('/api/ongkir/cost', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ origin_village_code: originAreaId, destination_village_code: destinationAreaId, weight: Math.max(parseInt(weightGram,10)||1,1) }),
        });
        const data = await res.json();
        if (!data.success || !Array.isArray(data.data) || !data.data.length) {
            showOngkirError(data.error || 'Layanan pengiriman tidak tersedia untuk area ini.');
            return;
        }
        renderOngkir(data.data);
    } catch (e) {
        showOngkirError('Gagal memuat ongkir. Silakan coba lagi.');
    } finally {
        isLoadingOngkir = false;
    }
}

function renderOngkir(list) {
    const content = document.getElementById('ongkirContent');
    content.innerHTML = '<div class="ongkir-list" id="ongkirList"></div>';
    const ol = document.getElementById('ongkirList');
    list.forEach((item, i) => {
        const div = document.createElement('div');
        div.className = 'ongkir-item';
        div.innerHTML = `
            <input type="radio" name="ongkir_radio" value="${i}" id="ok_${i}">
            <label for="ok_${i}" style="display:flex;align-items:center;gap:10px;flex:1;cursor:pointer;">
                <span class="ongkir-badge">${item.courier||'-'}</span>
                <span class="ongkir-name">${item.service||item.courier_name||'-'}</span>
            </label>
            <div style="text-align:right;flex-shrink:0;">
                <div class="ongkir-price">${fmtRp(parseInt(item.cost||0,10))}</div>
                <div class="ongkir-etd">${item.etd||'-'}</div>
            </div>`;
        div.addEventListener('click', () => { const r=div.querySelector('input'); if(r)r.checked=true; selectOngkir(div,item); });
        ol.appendChild(div);
    });
    const first = ol.querySelector('.ongkir-item');
    if (first) first.click();
}

function selectOngkir(el, item) {
    document.querySelectorAll('.ongkir-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');
    selectedCost = parseInt(item.cost||0, 10);
    document.getElementById('selCourier').value = String(item.courier||item.courier_name||'OTHER').trim();
    document.getElementById('selService').value = String(item.service||item.courier_name||'').trim();
    document.getElementById('selCost').value    = selectedCost;
    document.getElementById('btnPay').disabled  = false;
    document.getElementById('btnPayText').textContent = '✅ Lanjut ke Review Pembayaran';
    updateSummary(currentQty);
}

function showOngkirError(msg) {
    const el = document.getElementById('ongkirContent');
    if (el) el.innerHTML = `<div class="ongkir-error">⚠️ ${msg}</div>`;
    clearCourierSelection(); updateSummary(currentQty);
}

function clearCourierSelection() {
    selectedCost = 0;
    const btn = document.getElementById('btnPay');
    const txt = document.getElementById('btnPayText');
    document.getElementById('selCourier').value = '';
    document.getElementById('selService').value = '';
    document.getElementById('selCost').value    = '0';
    if (btn && PRODUCT_TYPE==='fisik' && SHIPPING_ENABLED) btn.disabled = true;
    if (txt && PRODUCT_TYPE==='fisik' && SHIPPING_ENABLED) txt.textContent = '⬆ Pilih Ongkir Dulu';
}

let alertTimer = null;
function showCheckoutAlert(message, type='error') {
    const el = document.getElementById('checkoutAlert');
    if (!el) return;
    el.textContent = message;
    el.classList.remove('error','success','show');
    el.classList.add(type==='success'?'success':'error');
    clearTimeout(alertTimer);
    requestAnimationFrame(() => el.classList.add('show'));
    alertTimer = setTimeout(() => el.classList.remove('show'), 3500);
}

// ===== SUBMIT =====
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (PRODUCT_TYPE==='fisik' && SHIPPING_ENABLED) {
        if (!ORIGIN_AREA_ID) { showCheckoutAlert('Penjual belum mengatur area asal pengiriman.'); return; }
        if (!destinationArea || !destVillageCodeEl.value) { showCheckoutAlert('Silakan pilih area tujuan pengiriman terlebih dahulu.'); return; }
        if (isLoadingOngkir) { showCheckoutAlert('Sedang menghitung ongkir. Mohon tunggu sebentar.'); return; }
        const costVal = parseInt(document.getElementById('selCost').value||'0', 10);
        if (costVal <= 0) {
            const errEl = document.querySelector('#ongkirContent .ongkir-error');
            showCheckoutAlert(errEl ? errEl.textContent.replace('⚠️','').trim() : 'Silakan pilih layanan pengiriman terlebih dahulu.');
            return;
        }
    }
    applyQty(parseInt(qtyDisplay?qtyDisplay.value:1, 10), { reloadOngkir: false });
    const btn=document.getElementById('btnPay'), spinner=document.getElementById('paySpinner'), btnTxt=document.getElementById('btnPayText');
    btn.disabled=true; spinner.style.display='inline-block'; btnTxt.textContent='Membuka Halaman Review...';
    this.submit();
});
</script>
</body>
</html>
