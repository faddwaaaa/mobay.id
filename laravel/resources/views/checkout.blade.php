<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $product->title ?? 'Produk' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:system-ui,-apple-system,sans-serif; background:#f9fafb; min-height:100vh; padding:20px 16px 40px; }
        .top-bar { max-width:500px; margin:0 auto 20px; display:flex; align-items:center; gap:12px; }
        .btn-back { width:36px; height:36px; border-radius:8px; border:1px solid #e5e7eb; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; color:#374151; transition:all .2s; flex-shrink:0; }
        .btn-back:hover { border-color:#2563eb; color:#2563eb; background:#eff6ff; }
        .top-bar-title { font-size:16px; font-weight:600; color:#111827; }
        .card { max-width:500px; margin:0 auto 16px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; }
        .card-header { padding:14px 16px; border-bottom:1px solid #e5e7eb; font-size:13px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; }
        .card-body { padding:16px; }
        .product-row { display:flex; gap:14px; align-items:flex-start; }
        .product-img { width:80px; height:80px; border-radius:8px; object-fit:cover; flex-shrink:0; border:1px solid #e5e7eb; }
        .product-img-ph { width:80px; height:80px; border-radius:8px; background:#eff6ff; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:28px; border:1px solid #e5e7eb; }
        .product-meta { flex:1; min-width:0; }
        .badge { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:600; padding:2px 8px; border-radius:20px; margin-bottom:6px; }
        .badge.fisik { background:#f0fdf4; color:#16a34a; }
        .badge.digital { background:#eff6ff; color:#2563eb; }
        .product-meta h2 { font-size:15px; font-weight:700; color:#111827; margin-bottom:6px; line-height:1.4; }
        .price-row { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .price-final { font-size:18px; font-weight:800; color:#2563eb; }
        .price-ori { font-size:13px; color:#9ca3af; text-decoration:line-through; }
        .disc-badge { background:#fee2e2; color:#dc2626; font-size:11px; font-weight:700; padding:2px 6px; border-radius:4px; }
        .seller-row { display:flex; align-items:center; gap:10px; padding:12px 0 0; margin-top:12px; border-top:1px solid #f3f4f6; }
        .seller-ava { width:32px; height:32px; border-radius:50%; object-fit:cover; flex-shrink:0; }
        .seller-ava-ph { width:32px; height:32px; border-radius:50%; background:#eff6ff; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
        .form-group { margin-bottom:14px; }
        label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
        .req { color:#ef4444; margin-left:2px; }
        input[type="text"], input[type="email"], input[type="tel"], textarea { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; color:#111827; background:#fff; transition:border-color .2s,box-shadow .2s; outline:none; font-family:inherit; }
        input:focus, textarea:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.08); }
        textarea { resize:vertical; min-height:80px; }
        .hint { font-size:12px; color:#6b7280; margin-top:4px; }

        /* QTY */
        .qty-wrap { display:flex; align-items:center; justify-content:space-between; background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:6px 8px; gap:8px; }
        .qty-info { font-size:12px; color:#6b7280; }
        .qty-ctrl { display:flex; align-items:center; background:#fff; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden; }
        .qty-btn { width:36px; height:36px; border:none; background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#374151; font-size:16px; font-weight:600; transition:all .15s; flex-shrink:0; user-select:none; }
        .qty-btn:hover:not(:disabled) { background:#eff6ff; color:#2563eb; }
        .qty-btn:disabled { color:#d1d5db; cursor:not-allowed; }
        .qty-input { min-width:52px; width:52px; text-align:center; font-size:15px; font-weight:700; color:#111827; border:none !important; border-left:1px solid #e5e7eb !important; border-right:1px solid #e5e7eb !important; border-radius:0 !important; padding:0 4px !important; height:36px; background:#fff; box-shadow:none !important; outline:none; -moz-appearance:textfield; }
        .qty-input::-webkit-inner-spin-button { -webkit-appearance:none; }
        .qty-input:focus { background:#eff6ff !important; color:#2563eb !important; }

        /* AUTOCOMPLETE KOTA */
        .city-wrap { position:relative; }
        .city-dd { position:absolute; top:calc(100% + 4px); left:0; right:0; background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.10); z-index:100; max-height:220px; overflow-y:auto; display:none; }
        .city-dd.show { display:block; }
        .city-item { padding:10px 14px; font-size:13px; color:#374151; cursor:pointer; border-bottom:1px solid #f3f4f6; transition:background .1s; }
        .city-item:last-child { border-bottom:none; }
        .city-item:hover, .city-item.active { background:#eff6ff; color:#2563eb; }
        .city-badge { display:inline-flex; align-items:center; gap:5px; margin-top:5px; padding:4px 10px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; font-size:12px; color:#15803d; }

        /* ONGKIR */
        .ongkir-section { margin-top:14px; }
        .ongkir-loading { display:flex; align-items:center; gap:8px; padding:12px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; color:#6b7280; }
        .ongkir-loading svg { animation:spin 1s linear infinite; flex-shrink:0; }
        .ongkir-error { padding:10px 12px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; font-size:12px; color:#dc2626; }
        .ongkir-list { display:flex; flex-direction:column; gap:8px; }
        .ongkir-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border:1.5px solid #e5e7eb; border-radius:10px; cursor:pointer; transition:all .15s; background:#fff; }
        .ongkir-item:hover { border-color:#2563eb; background:#f8faff; }
        .ongkir-item.selected { border-color:#2563eb; background:#eff6ff; }
        .ongkir-item input[type="radio"] { accent-color:#2563eb; width:16px; height:16px; flex-shrink:0; }
        .ongkir-courier { font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; background:#e0e7ff; color:#4338ca; flex-shrink:0; }
        .ongkir-service { font-size:13px; font-weight:600; color:#111827; flex:1; }
        .ongkir-desc { font-size:11px; color:#6b7280; }
        .ongkir-etd { font-size:11px; color:#6b7280; flex-shrink:0; }
        .ongkir-price { font-size:14px; font-weight:700; color:#2563eb; flex-shrink:0; }
        @keyframes spin { to { transform:rotate(360deg); } }

        /* SUMMARY */
        .sum-row { display:flex; justify-content:space-between; align-items:center; font-size:14px; color:#374151; padding:6px 0; }
        .sum-row.total { font-weight:700; font-size:16px; color:#111827; padding-top:12px; margin-top:6px; border-top:1px solid #e5e7eb; }
        .sum-row.total .total-amt { color:#2563eb; font-size:18px; }
        .sum-ph { color:#9ca3af; font-style:italic; font-size:12px; }

        .btn-pay { width:100%; max-width:500px; margin:0 auto; display:block; background:#2563eb; color:#fff; border:none; border-radius:10px; padding:14px; font-size:15px; font-weight:700; cursor:pointer; transition:background .2s; }
        .btn-pay:hover:not(:disabled) { background:#1d4ed8; }
        .btn-pay:disabled { background:#93c5fd; cursor:not-allowed; }
        .spinner { display:none; width:16px; height:16px; border:2px solid rgba(255,255,255,.4); border-top-color:#fff; border-radius:50%; animation:spin .7s linear infinite; margin-right:8px; }
        .alert { max-width:500px; margin:0 auto 16px; padding:12px 14px; border-radius:8px; font-size:13px; display:flex; align-items:flex-start; gap:8px; }
        .alert-info { background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; }
        .secure { max-width:500px; margin:12px auto 0; text-align:center; font-size:12px; color:#9ca3af; display:flex; align-items:center; justify-content:center; gap:5px; }
    </style>
</head>
<body>

<div class="top-bar">
    <a href="javascript:history.back()" class="btn-back">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div class="top-bar-title">Checkout</div>
</div>

@if($product->product_type === 'digital')
<div class="alert alert-info">
    <span>📦</span>
    <span>Produk digital — file dikirim otomatis ke email Anda setelah pembayaran berhasil.</span>
</div>
@endif

{{-- DETAIL PRODUK --}}
<div class="card">
    <div class="card-header">Detail Produk</div>
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
                    <div style="margin-top:5px;font-size:11px;color:#6b7280;">⚖️ Berat: {{ $product->weight }}gr</div>
                @endif
            </div>
        </div>
        @if($seller)
        <div class="seller-row">
            @if($seller->avatar)
                <img src="{{ asset('storage/'.$seller->avatar) }}" class="seller-ava" alt="{{ $seller->name }}">
            @else
                <div class="seller-ava-ph">👤</div>
            @endif
            <div style="font-size:13px;color:#374151;">
                Dijual oleh <strong>{{ $seller->name }}</strong>
                @if($seller->origin_city_name)
                    <span style="font-size:11px;color:#6b7280;"> · 📍 {{ $seller->origin_city_name }}</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<form id="checkoutForm" autocomplete="off">
    @csrf

    {{-- DATA PEMBELI --}}
    <div class="card">
        <div class="card-header">Data Pembeli</div>
        <div class="card-body">
            <div class="form-group">
                <label>Nama Lengkap <span class="req">*</span></label>
                <input type="text" name="buyer_name" placeholder="Nama lengkap Anda"
                       value="{{ auth()->user()->name ?? '' }}" required>
            </div>
            <div class="form-group">
                <label>Email <span class="req">*</span></label>
                <input type="email" name="buyer_email" placeholder="email@contoh.com"
                       value="{{ auth()->user()->email ?? '' }}" required>
                @if($product->product_type === 'digital')
                    <div class="hint">📧 File digital dikirim ke email ini.</div>
                @endif
            </div>
            <div class="form-group">
                <label>No. WhatsApp <span class="req">*</span></label>
                <input type="tel" name="buyer_phone" placeholder="08xxxxxxxxxx" required>
            </div>

            @if($product->product_type === 'fisik')

            {{-- KOTA TUJUAN --}}
            <div class="form-group">
                <label>Kota Tujuan Pengiriman <span class="req">*</span></label>
                <div class="city-wrap">
                    <input type="text" id="citySearch"
                           placeholder="Ketik nama kota, misal: Surabaya..."
                           autocomplete="off">
                    <div class="city-dd" id="cityDropdown"></div>
                </div>
                <div id="cityBadge" style="display:none" class="city-badge">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="cityBadgeText"></span>
                </div>
                <input type="hidden" name="destination_city_name" id="destCityName">
            </div>

            <div class="form-group">
                <label>Alamat Lengkap <span class="req">*</span></label>
                <textarea name="buyer_address" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan..." required></textarea>
            </div>
            <div class="form-group">
                <label>Catatan untuk Penjual</label>
                <input type="text" name="buyer_notes" placeholder="Warna, ukuran, atau keterangan lain (opsional)">
            </div>

            {{-- QTY --}}
            @php $maxQty = $product->purchase_limit ?? ($product->stock ?? 99); @endphp
            <div class="form-group">
                <label>Jumlah <span class="req">*</span></label>
                <div class="qty-wrap">
                    <div class="qty-info">
                        @if($product->purchase_limit) Maks. {{ $product->purchase_limit }}/transaksi
                        @elseif($product->stock) Stok: {{ $product->stock }}
                        @else Pilih jumlah @endif
                    </div>
                    <div class="qty-ctrl">
                        <button type="button" class="qty-btn" id="qtyMinus" onclick="changeQty(-1)" disabled>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/></svg>
                        </button>
                        <input type="number" id="qtyDisplay" class="qty-input" value="1" min="1" max="{{ $maxQty }}" inputmode="numeric">
                        <button type="button" class="qty-btn" id="qtyPlus" onclick="changeQty(1)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>
                </div>
                <input type="number" name="qty" id="qtyHidden" value="1" min="1" max="{{ $maxQty }}" style="display:none">
            </div>

            {{-- PILIH ONGKIR --}}
            <div class="form-group ongkir-section" id="ongkirSection" style="display:none">
                <label>Layanan Pengiriman <span class="req">*</span></label>
                <div id="ongkirContent">
                    <div class="ongkir-loading">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="4" style="opacity:.3"/>
                            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:.8"/>
                        </svg>
                        Mengambil data ongkir...
                    </div>
                </div>
                <input type="hidden" name="selected_courier"      id="selCourier">
                <input type="hidden" name="selected_service"       id="selService">
                <input type="hidden" name="selected_ongkir_cost"   id="selCost" value="0">
            </div>

            @else
                <input type="hidden" name="qty" value="1">
            @endif
        </div>
    </div>

    <input type="hidden" name="payment_method" value="gopay">

    {{-- RINGKASAN --}}
    <div class="card">
        <div class="card-header">Ringkasan Pembayaran</div>
        <div class="card-body">
            <div class="sum-row">
                <span>Harga satuan</span>
                <span>Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}</span>
            </div>
            @if($product->product_type === 'fisik')
            <div class="sum-row">
                <span>Jumlah</span>
                <span id="sumQty">1</span>
            </div>
            <div class="sum-row">
                <span>Subtotal</span>
                <span id="sumSubtotal">Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}</span>
            </div>
            <div class="sum-row">
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Ongkos Kirim
                </span>
                <span id="sumShipping"><span class="sum-ph">Pilih kota & kurir</span></span>
            </div>
            @endif
            <div class="sum-row total">
                <span>Total Pembayaran</span>
                <span class="total-amt" id="sumTotal">Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <button type="submit" class="btn-pay" id="btnPay" @if($product->product_type==='fisik') disabled @endif>
        <span class="spinner" id="paySpinner"></span>
        <span id="btnPayText">{{ $product->product_type === 'fisik' ? 'Pilih Kurir Dulu' : 'Bayar Sekarang' }}</span>
    </button>
</form>

<div class="secure">🔒 Pembayaran aman & terenkripsi via Midtrans</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
// ===== KONSTANTA =====
const UNIT_PRICE       = {{ $product->discount ?? $product->price ?? 0 }};
const MAX_QTY          = {{ $product->purchase_limit ?? ($product->stock ?? 99) }};
const PRODUCT_TYPE     = '{{ $product->product_type }}';
const ORIGIN_CITY_NAME = '{{ $seller->origin_city_name ?? '' }}'; // nama kota penjual
const WEIGHT_GRAM      = {{ $product->weight ?? 1000 }};

let currentQty     = 1;
let selectedCost   = 0;
let destCityName   = '';

function fmtRp(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n); }

// ===== SUMMARY =====
function updateSummary(qty) {
    qty = Math.min(Math.max(qty, 1), MAX_QTY);
    const subtotal = UNIT_PRICE * qty;
    const total    = subtotal + selectedCost;
    const elQ = document.getElementById('sumQty');
    const elS = document.getElementById('sumSubtotal');
    const elT = document.getElementById('sumTotal');
    if (elQ) elQ.textContent = qty;
    if (elS) elS.textContent = fmtRp(subtotal);
    if (elT) elT.textContent = fmtRp(total);
}

// ===== QTY STEPPER =====
const qtyDisplay = document.getElementById('qtyDisplay');
const qtyHidden  = document.getElementById('qtyHidden');
const qtyMinus   = document.getElementById('qtyMinus');
const qtyPlus    = document.getElementById('qtyPlus');

function applyQty(n) {
    if (isNaN(n) || n < 1) n = 1;
    if (n > MAX_QTY)       n = MAX_QTY;
    currentQty = n;
    if (qtyDisplay) qtyDisplay.value = n;
    if (qtyHidden)  qtyHidden.value  = n;
    if (qtyMinus)   qtyMinus.disabled = n <= 1;
    if (qtyPlus)    qtyPlus.disabled  = n >= MAX_QTY;
    updateSummary(n);
}
function changeQty(d) { applyQty(currentQty + d); }

if (qtyDisplay) {
    qtyDisplay.addEventListener('input', function() {
        const v = parseInt(this.value, 10);
        if (!isNaN(v)) { currentQty = v; applyQty(v); }
    });
    qtyDisplay.addEventListener('blur', function() { applyQty(parseInt(this.value, 10)); });
    qtyDisplay.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); this.blur(); }
        if (!['Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Tab','Home','End'].includes(e.key) && !/^[0-9]$/.test(e.key)) e.preventDefault();
    });
}

// ===== CITY AUTOCOMPLETE =====
const citySearch   = document.getElementById('citySearch');
const cityDropdown = document.getElementById('cityDropdown');
const cityBadge    = document.getElementById('cityBadge');
const cityBadgeText= document.getElementById('cityBadgeText');
const destCityInp  = document.getElementById('destCityName');
let cityTimer = null;

if (citySearch) {
    citySearch.addEventListener('input', function() {
        const q = this.value.trim();
        clearTimeout(cityTimer);
        if (q.length < 2) { cityDropdown.classList.remove('show'); return; }
        cityDropdown.innerHTML = '<div class="city-item" style="color:#9ca3af">🔍 Mencari kota...</div>';
        cityDropdown.classList.add('show');
        cityTimer = setTimeout(() => searchCity(q), 350);
    });
    citySearch.addEventListener('keydown', function(e) {
        const items  = cityDropdown.querySelectorAll('.city-item');
        const active = cityDropdown.querySelector('.city-item.active');
        let idx = Array.from(items).indexOf(active);
        if (e.key === 'ArrowDown') { e.preventDefault(); idx = Math.min(idx+1, items.length-1); items.forEach(i=>i.classList.remove('active')); if(items[idx])items[idx].classList.add('active'); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); idx = Math.max(idx-1,0); items.forEach(i=>i.classList.remove('active')); if(items[idx])items[idx].classList.add('active'); }
        else if (e.key === 'Enter') { e.preventDefault(); if(active)active.click(); }
        else if (e.key === 'Escape') cityDropdown.classList.remove('show');
    });
    document.addEventListener('click', e => { if (!citySearch.contains(e.target)) cityDropdown.classList.remove('show'); });
}

async function searchCity(q) {
    try {
        const res  = await fetch('/api/ongkir/cities?q=' + encodeURIComponent(q));
        const data = await res.json();
        if (!data.length) {
            cityDropdown.innerHTML = '<div class="city-item" style="color:#9ca3af">Kota tidak ditemukan</div>';
            return;
        }
        cityDropdown.innerHTML = '';
        data.forEach(city => {
            const el = document.createElement('div');
            el.className = 'city-item';
            el.innerHTML = `<strong>${city.city_name}</strong> <span style="color:#9ca3af;font-size:11px;">${city.province}</span>`;
            el.addEventListener('click', () => selectCity(city));
            cityDropdown.appendChild(el);
        });
    } catch(e) {
        cityDropdown.innerHTML = '<div class="city-item" style="color:#dc2626">Gagal memuat kota</div>';
    }
}

function selectCity(city) {
    citySearch.value    = city.city_name + ', ' + city.province;
    destCityInp.value   = city.city_name;
    destCityName        = city.city_name;
    cityBadgeText.textContent = city.city_name + ', ' + city.province;
    cityBadge.style.display = 'inline-flex';
    cityDropdown.classList.remove('show');

    if (ORIGIN_CITY_NAME) {
        loadOngkir(ORIGIN_CITY_NAME, city.city_name, WEIGHT_GRAM);
    } else {
        showOngkirError('Penjual belum mengatur kota asal. Hubungi penjual terlebih dahulu.');
    }
}

// ===== LOAD ONGKIR =====
async function loadOngkir(origin, destination, weight) {
    const section = document.getElementById('ongkirSection');
    const content = document.getElementById('ongkirContent');
    if (!section || !content) return;

    section.style.display = 'block';
    content.innerHTML = `<div class="ongkir-loading"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="4" style="opacity:.3"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:.8"/></svg>Mengambil data ongkir dari ${origin} → ${destination}...</div>`;

    // Reset
    selectedCost = 0;
    document.getElementById('selCourier').value = '';
    document.getElementById('selService').value  = '';
    document.getElementById('selCost').value     = '0';
    document.getElementById('sumShipping').innerHTML = '<span class="sum-ph">Memilih kurir...</span>';
    document.getElementById('btnPay').disabled = true;
    document.getElementById('btnPayText').textContent = 'Pilih Kurir Dulu';
    updateSummary(currentQty);

    try {
        const res  = await fetch('/api/ongkir/cost', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                origin_city:      origin,
                destination_city: destination,
                weight:           weight,
            }),
        });
        const data = await res.json();

        if (!data.success || !data.data || !data.data.length) {
            showOngkirError(data.error || 'Tidak ada layanan pengiriman tersedia untuk rute ini.');
            return;
        }

        renderOngkir(data.data);
    } catch(e) {
        showOngkirError('Gagal terhubung ke server. Coba lagi.');
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
            <label for="ok_${i}" style="display:flex;align-items:center;gap:8px;flex:1;cursor:pointer;">
                <span class="ongkir-courier">${item.courier}</span>
                <span class="ongkir-service">${item.service} <span class="ongkir-desc">· ${item.description}</span></span>
            </label>
            <div style="text-align:right;flex-shrink:0;">
                <div class="ongkir-price">${fmtRp(item.cost)}</div>
                <div class="ongkir-etd">⏱ ${item.etd || '-'}</div>
            </div>`;
        div.addEventListener('click', () => {
            div.querySelector('input').checked = true;
            selectOngkir(div, item);
        });
        ol.appendChild(div);
    });
}

function selectOngkir(el, item) {
    document.querySelectorAll('.ongkir-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');
    selectedCost = item.cost;
    document.getElementById('selCourier').value = item.courier;
    document.getElementById('selService').value  = item.service;
    document.getElementById('selCost').value     = item.cost;
    document.getElementById('sumShipping').textContent = fmtRp(item.cost);
    updateSummary(currentQty);
    document.getElementById('btnPay').disabled = false;
    document.getElementById('btnPayText').textContent = 'Bayar Sekarang';
}

function showOngkirError(msg) {
    const el = document.getElementById('ongkirContent');
    if (el) el.innerHTML = `<div class="ongkir-error">⚠️ ${msg}</div>`;
}

// ===== SUBMIT =====
document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (PRODUCT_TYPE === 'fisik') {
        if (!destCityName) { alert('Pilih kota tujuan pengiriman terlebih dahulu'); return; }
        if (!document.getElementById('selCourier').value) { alert('Pilih layanan pengiriman terlebih dahulu'); return; }
    }
    applyQty(parseInt(qtyDisplay ? qtyDisplay.value : 1, 10));

    const btn     = document.getElementById('btnPay');
    const spinner = document.getElementById('paySpinner');
    const btnTxt  = document.getElementById('btnPayText');
    btn.disabled  = true; spinner.style.display = 'inline-block'; btnTxt.textContent = 'Memproses...';

    const fd   = new FormData(this);
    const data = Object.fromEntries(fd.entries());

    try {
        const res = await fetch('{{ route("checkout.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ product_id: {{ $product->id }}, ...data }),
        });
        const result = await res.json();
        if (!res.ok || result.error) throw new Error(result.message || 'Terjadi kesalahan.');

        snap.pay(result.snap_token, {
            onSuccess: () => window.location.href = '{{ route("checkout.success") }}?order_id=' + result.order_id,
            onPending: () => window.location.href = '{{ route("checkout.pending") }}?order_id=' + result.order_id,
            onError:   () => { alert('Pembayaran gagal. Coba lagi.'); resetBtn(); },
            onClose:   () => resetBtn(),
        });
    } catch(err) {
        alert(err.message || 'Gagal menghubungi server.'); resetBtn();
    }

    function resetBtn() {
        btn.disabled = (PRODUCT_TYPE === 'fisik' && !document.getElementById('selCourier').value);
        spinner.style.display = 'none';
        btnTxt.textContent = 'Bayar Sekarang';
    }
});
</script>
</body>
</html>