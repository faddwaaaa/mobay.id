<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $product->title ?? 'Produk' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f9fafb;
            min-height: 100vh;
            padding: 20px 16px 40px;
        }

        .top-bar {
            max-width: 500px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-back {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            color: #374151;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .btn-back:hover {
            border-color: #2563eb;
            color: #2563eb;
            background: #eff6ff;
        }

        .top-bar-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .card {
            max-width: 500px;
            margin: 0 auto 16px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-body {
            padding: 16px;
        }

        .product-row {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .product-img {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            background: #f3f4f6;
            flex-shrink: 0;
            border: 1px solid #e5e7eb;
        }

        .product-img-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            background: #eff6ff;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            border: 1px solid #e5e7eb;
        }

        .product-meta {
            flex: 1;
            min-width: 0;
        }

        .product-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            margin-bottom: 6px;
        }

        .product-type-badge.digital {
            background: #eff6ff;
            color: #2563eb;
        }

        .product-type-badge.umkm {
            background: #f0fdf4;
            color: #16a34a;
        }

        .product-meta h2 {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
            line-height: 1.4;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .price-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .price-final {
            font-size: 18px;
            font-weight: 800;
            color: #2563eb;
        }

        .price-original {
            font-size: 13px;
            color: #9ca3af;
            text-decoration: line-through;
        }

        .price-discount-badge {
            background: #fee2e2;
            color: #dc2626;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .seller-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 0 0;
            margin-top: 12px;
            border-top: 1px solid #f3f4f6;
        }

        .seller-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background: #e5e7eb;
            flex-shrink: 0;
        }

        .seller-avatar-placeholder {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .seller-info {
            font-size: 13px;
            color: #374151;
        }

        .seller-info span {
            font-weight: 600;
            color: #111827;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        label .required {
            color: #ef4444;
            margin-left: 2px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            font-family: inherit;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        input::placeholder,
        textarea::placeholder {
            color: #9ca3af;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .field-hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .stock-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            margin-top: 4px;
        }

        .stock-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .stock-dot.in-stock { background: #16a34a; }
        .stock-dot.low-stock { background: #f59e0b; }
        .stock-dot.out-stock { background: #ef4444; }

        /* =========================================================
           QTY STEPPER
        ========================================================= */
        .qty-stepper-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 6px 8px;
            gap: 8px;
        }

        .qty-info {
            font-size: 12px;
            color: #6b7280;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 0;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border: none;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #374151;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.15s;
            flex-shrink: 0;
            user-select: none;
        }

        .qty-btn:hover:not(:disabled) {
            background: #eff6ff;
            color: #2563eb;
        }

        .qty-btn:active:not(:disabled) {
            background: #dbeafe;
            transform: scale(0.92);
        }

        .qty-btn:disabled {
            color: #d1d5db;
            cursor: not-allowed;
        }

        /* Input qty yang bisa diklik & diketik */
        .qty-input-editable {
            min-width: 52px;
            width: 52px;
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            border: none !important;
            border-left: 1px solid #e5e7eb !important;
            border-right: 1px solid #e5e7eb !important;
            border-radius: 0 !important;
            padding: 0 4px !important;
            height: 36px;
            line-height: 36px;
            background: #fff;
            box-shadow: none !important;
            outline: none;
            cursor: text;
            transition: background 0.15s, color 0.15s;
            -moz-appearance: textfield;
        }

        .qty-input-editable::-webkit-inner-spin-button,
        .qty-input-editable::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .qty-input-editable:focus {
            background: #eff6ff !important;
            color: #2563eb !important;
            box-shadow: none !important;
            border-color: transparent !important;
            border-left-color: #bfdbfe !important;
            border-right-color: #bfdbfe !important;
        }

        /* Hidden input untuk submit */
        input#qty {
            display: none;
        }

        /* =========================================================
           ORDER SUMMARY
        ========================================================= */
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #374151;
            padding: 6px 0;
        }

        .summary-row.total {
            font-weight: 700;
            font-size: 16px;
            color: #111827;
            padding-top: 12px;
            margin-top: 6px;
            border-top: 1px solid #e5e7eb;
        }

        .summary-row.total .total-amount {
            color: #2563eb;
            font-size: 18px;
        }

        .btn-pay {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            display: block;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            text-align: center;
            letter-spacing: 0.3px;
        }

        .btn-pay:hover:not(:disabled) {
            background: #1d4ed8;
        }

        .btn-pay:disabled {
            background: #93c5fd;
            cursor: not-allowed;
        }

        .btn-pay .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .alert {
            max-width: 500px;
            margin: 0 auto 16px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .alert-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .alert-warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .out-of-stock-overlay {
            text-align: center;
            padding: 32px 20px;
        }

        .out-of-stock-overlay .icon {
            font-size: 48px;
            margin-bottom: 12px;
        }

        .out-of-stock-overlay h3 {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .out-of-stock-overlay p {
            font-size: 13px;
            color: #6b7280;
        }

        .secure-note {
            max-width: 500px;
            margin: 12px auto 0;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
    </style>
</head>
<body>

    <!-- TOP BAR -->
    <div class="top-bar">
        <a href="javascript:history.back()" class="btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="top-bar-title">Checkout</div>
    </div>

    @if(!$product)
        <div class="alert alert-warning" style="max-width:500px;margin:0 auto;">
            ⚠️ Produk tidak ditemukan.
        </div>
    @else

    @if($product->product_type === 'digital')
        <div class="alert alert-info" style="max-width:500px;margin:0 auto 16px;">
            <span>📦</span>
            <span>Produk digital. File akan dikirim otomatis ke email Anda setelah pembayaran berhasil.</span>
        </div>
    @endif

    @if($product->product_type === 'umkm' && $product->stock !== null && $product->stock <= 0)
        <div class="card">
            <div class="card-body">
                <div class="out-of-stock-overlay">
                    <div class="icon">😔</div>
                    <h3>Stok Habis</h3>
                    <p>Produk ini sedang tidak tersedia. Coba lagi nanti.</p>
                </div>
            </div>
        </div>
    @else

    <div class="card">
        <div class="card-header">Detail Produk</div>
        <div class="card-body">
            <div class="product-row">
                @if($product->images && $product->images->count() > 0)
                    <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                         class="product-img"
                         alt="{{ $product->title }}">
                @else
                    <div class="product-img-placeholder">🛍️</div>
                @endif

                <div class="product-meta">
                    <span class="product-type-badge {{ $product->product_type }}">
                        @if($product->product_type === 'digital')
                            💾 Digital
                        @else
                            📦 Fisik
                        @endif
                    </span>
                    <h2>{{ $product->title }}</h2>
                    <div class="price-row">
                        <span class="price-final">
                            Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}
                        </span>
                        @if($product->discount && $product->discount < $product->price)
                            <span class="price-original">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                            @php
                                $disc = round((($product->price - $product->discount) / $product->price) * 100);
                            @endphp
                            <span class="price-discount-badge">-{{ $disc }}%</span>
                        @endif
                    </div>

                    @if($product->product_type === 'umkm' && $product->stock !== null)
                        <div class="stock-info">
                            @if($product->stock > 10)
                                <div class="stock-dot in-stock"></div>
                                <span style="color:#16a34a">Stok tersedia ({{ $product->stock }})</span>
                            @elseif($product->stock > 0)
                                <div class="stock-dot low-stock"></div>
                                <span style="color:#f59e0b">Stok terbatas ({{ $product->stock }} tersisa)</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($seller)
                <div class="seller-row">
                    @if($seller->avatar)
                        <img src="{{ asset('storage/' . $seller->avatar) }}" class="seller-avatar" alt="{{ $seller->name }}">
                    @else
                        <div class="seller-avatar-placeholder">👤</div>
                    @endif
                    <div class="seller-info">
                        Dijual oleh <span>{{ $seller->name }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <form id="checkoutForm" autocomplete="off">
        @csrf

        <div class="card">
            <div class="card-header">Data Pembeli</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="buyer_name" id="buyer_name"
                           placeholder="Contoh: Budi Santoso"
                           value="{{ auth()->user()->name ?? '' }}"
                           required>
                </div>
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="buyer_email" id="buyer_email"
                           placeholder="email@contoh.com"
                           value="{{ auth()->user()->email ?? '' }}"
                           required>
                    @if($product->product_type === 'digital')
                        <div class="field-hint">📧 File digital akan dikirim ke email ini.</div>
                    @endif
                </div>
                <div class="form-group">
                    <label>No. WhatsApp <span class="required">*</span></label>
                    <input type="tel" name="buyer_phone" id="buyer_phone"
                           placeholder="08xxxxxxxxxx" required>
                </div>

                @if($product->product_type === 'umkm')
                    <div class="form-group">
                        <label>Alamat Pengiriman <span class="required">*</span></label>
                        <textarea name="buyer_address" id="buyer_address"
                                  placeholder="Tulis alamat lengkap termasuk kota dan kode pos..."
                                  required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Catatan untuk Penjual</label>
                        <input type="text" name="buyer_notes" id="buyer_notes"
                               placeholder="Warna, ukuran, atau keterangan lain (opsional)">
                    </div>
                @endif

                @if($product->product_type === 'umkm')
                    @php
                        $maxQty = $product->purchase_limit ?? ($product->stock ?? 99);
                    @endphp
                    <div class="form-group">
                        <label>Jumlah <span class="required">*</span></label>

                        {{-- Stepper UI --}}
                        <div class="qty-stepper-wrapper">
                            <div class="qty-info">
                                @if($product->purchase_limit)
                                    Maks. {{ $product->purchase_limit }} per transaksi
                                @elseif($product->stock)
                                    Stok: {{ $product->stock }} tersedia
                                @else
                                    Pilih jumlah
                                @endif
                            </div>
                            <div class="qty-controls">
                                <button type="button" class="qty-btn" id="qtyMinus" onclick="changeQty(-1)" disabled>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M5 12h14"/>
                                    </svg>
                                </button>
                                {{-- Input yang bisa diklik & diketik langsung, menggantikan div display --}}
                                <input
                                    type="number"
                                    id="qtyDisplayInput"
                                    class="qty-input-editable"
                                    value="1"
                                    min="1"
                                    max="{{ $maxQty }}"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    autocomplete="off"
                                    title="Ketik jumlah atau gunakan tombol +/-"
                                >
                                <button type="button" class="qty-btn" id="qtyPlus" onclick="changeQty(1)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M12 5v14M5 12h14"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Hidden input yang dikirim ke server --}}
                        <input type="number" name="qty" id="qty" value="1"
                               min="1" max="{{ $maxQty }}" required>
                    </div>
                @else
                    <input type="hidden" name="qty" value="1">
                @endif
            </div>
        </div>

        <input type="hidden" name="payment_method" value="gopay">

        <div class="card">
            <div class="card-header">Ringkasan Pembayaran</div>
            <div class="card-body">
                <div class="summary-row">
                    <span>Harga satuan</span>
                    <span id="summary_unit_price">
                        Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}
                    </span>
                </div>
                @if($product->product_type === 'umkm')
                    <div class="summary-row">
                        <span>Jumlah</span>
                        <span id="summary_qty">1</span>
                    </div>
                @endif
                <div class="summary-row total">
                    <span>Total</span>
                    <span class="total-amount" id="summary_total">
                        Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-pay" id="btnPay">
            <span class="spinner" id="paySpinner"></span>
            <span id="btnPayText">Bayar Sekarang</span>
        </button>
    </form>

    <div class="secure-note">
        🔒 Pembayaran aman & terenkripsi melalui Midtrans
    </div>

    @endif
    @endif

    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        const UNIT_PRICE = {{ $product->discount ?? $product->price ?? 0 }};
        const MAX_QTY    = {{ $product->purchase_limit ?? ($product->stock ?? 99) }};
        let currentQty   = 1;

        const qtyDisplayInput = document.getElementById('qtyDisplayInput');
        const qtyHidden       = document.getElementById('qty');
        const qtyMinus        = document.getElementById('qtyMinus');
        const qtyPlus         = document.getElementById('qtyPlus');

        /** Terapkan qty baru: update semua UI & hidden field */
        function applyQty(newQty) {
            // Clamp ke range valid
            if (isNaN(newQty) || newQty < 1) newQty = 1;
            if (newQty > MAX_QTY) newQty = MAX_QTY;

            currentQty = newQty;

            // Sync semua elemen
            if (qtyDisplayInput) qtyDisplayInput.value = currentQty;
            if (qtyHidden)       qtyHidden.value        = currentQty;

            // State tombol
            if (qtyMinus) qtyMinus.disabled = currentQty <= 1;
            if (qtyPlus)  qtyPlus.disabled  = currentQty >= MAX_QTY;

            updateTotal();
        }

        /** Dipanggil oleh tombol +/- */
        function changeQty(delta) {
            applyQty(currentQty + delta);
        }

        /** Handler untuk input langsung */
        if (qtyDisplayInput) {
            // Saat mengetik: sinkron real-time (boleh kosong sementara)
            qtyDisplayInput.addEventListener('input', function () {
                const raw = parseInt(this.value, 10);
                if (!isNaN(raw)) {
                    // Update currentQty tanpa clamp dulu agar tidak menginterrupt pengetikan
                    currentQty = raw;
                    // Tapi tetap update tombol & total pakai nilai yang sudah di-clamp
                    const clamped = Math.min(Math.max(raw, 1), MAX_QTY);
                    if (qtyHidden) qtyHidden.value = clamped;
                    if (qtyMinus)  qtyMinus.disabled = clamped <= 1;
                    if (qtyPlus)   qtyPlus.disabled  = clamped >= MAX_QTY;
                    updateTotal(clamped);
                }
            });

            // Saat blur (klik di luar): paksa nilai valid & sinkron
            qtyDisplayInput.addEventListener('blur', function () {
                applyQty(parseInt(this.value, 10));
            });

            // Enter = konfirmasi dan blur
            qtyDisplayInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.blur();
                }
                // Blokir karakter non-angka kecuali Backspace, Delete, Arrow, Tab
                const allowed = ['Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Tab','Home','End'];
                if (!allowed.includes(e.key) && !/^[0-9]$/.test(e.key)) {
                    e.preventDefault();
                }
            });

            // Pilih semua teks saat diklik agar mudah diganti
            qtyDisplayInput.addEventListener('focus', function () {
                this.select();
            });
        }

        function updateTotal(overrideQty) {
            const q     = overrideQty ?? currentQty;
            const clamped = Math.min(Math.max(q, 1), MAX_QTY);
            const total = UNIT_PRICE * clamped;
            const fmt   = new Intl.NumberFormat('id-ID').format(total);
            const summaryQty = document.getElementById('summary_qty');
            if (summaryQty) summaryQty.textContent = clamped;
            document.getElementById('summary_total').textContent = 'Rp ' + fmt;
        }

        document.getElementById('checkoutForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Pastikan qty valid sebelum submit
            applyQty(parseInt(qtyDisplayInput ? qtyDisplayInput.value : 1, 10));

            const btn     = document.getElementById('btnPay');
            const spinner = document.getElementById('paySpinner');
            const btnText = document.getElementById('btnPayText');

            btn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Memproses...';

            const formData = new FormData(this);
            const data     = Object.fromEntries(formData.entries());

            try {
                const res = await fetch('{{ route("checkout.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ product_id: {{ $product->id }}, ...data })
                });

                const result = await res.json();

                if (!res.ok || result.error) {
                    throw new Error(result.message || 'Terjadi kesalahan.');
                }

                snap.pay(result.snap_token, {
                    onSuccess: function () {
                        window.location.href = '{{ route("checkout.success") }}?order_id=' + result.order_id;
                    },
                    onPending: function () {
                        window.location.href = '{{ route("checkout.pending") }}?order_id=' + result.order_id;
                    },
                    onError: function () {
                        alert('Pembayaran gagal. Silakan coba lagi.');
                        resetBtn();
                    },
                    onClose: function () { resetBtn(); }
                });

            } catch (err) {
                alert(err.message || 'Gagal menghubungi server. Coba lagi.');
                resetBtn();
            }

            function resetBtn() {
                btn.disabled = false;
                spinner.style.display = 'none';
                btnText.textContent = 'Bayar Sekarang';
            }
        });
    </script>
</body>
</html>