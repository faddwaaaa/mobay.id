<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Metode Pembayaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --ink:#0f172a;
            --muted:#64748b;
            --line:#e5e7eb;
            --brand:#2356e8;
            --brand-light:#eff6ff;
            --success:#10b981;
            --warning:#f59e0b;
            --bg:#f8fafc;
            --radius:16px;
            --radius-sm:10px;
        }
        body {
            font-family: system-ui,-apple-system,sans-serif;
            background: linear-gradient(135deg, #e8efff 0%, #f0f4ff 50%, #eef2ff 100%);
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

        .grid { display:grid; grid-template-columns: 1.2fr .8fr; gap: 14px; }
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; overflow:hidden; box-shadow: 0 8px 24px rgba(15,23,42,.05); }
        .hd { padding: 12px 14px; border-bottom:1px solid #eef2f7; font-size:12px; letter-spacing:.07em; text-transform:uppercase; color:#6b7280; font-weight:800; }
        .bd { padding: 12px 14px; }

        /* Payment Methods */
        .payment-methods { display: grid; gap: 12px; }
        .payment-method {
            border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; cursor: pointer;
            transition: all .2s; position: relative;
        }
        .payment-method:hover { border-color: var(--brand); background: var(--brand-light); }
        .payment-method.selected {
            border-color: var(--brand); background: var(--brand-light);
            box-shadow: 0 0 0 3px rgba(35,86,232,.1);
        }
        .payment-method input[type="radio"] { display: none; }
        .method-header { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
        .method-icon {
            width: 48px; height: 48px; border-radius: 10px; background: #f1f5f9;
            display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; color: #475569;
        }
        .method-info { flex: 1; }
        .method-name { font-weight: 700; font-size: 16px; color: var(--ink); margin-bottom: 2px; }
        .method-desc { font-size: 13px; color: var(--muted); }
        .method-fee {
            position: absolute; top: 16px; right: 16px;
            background: var(--success); color: white; padding: 4px 8px;
            border-radius: 6px; font-size: 12px; font-weight: 700;
        }
        .payment-method.selected .method-fee { background: var(--brand); }

        /* Summary */
        .row { display:flex; justify-content:space-between; gap:12px; padding:8px 0; border-bottom:1px dashed #edf2f7; font-size:13px; }
        .row:last-child { border-bottom:none; }
        .k { color:var(--muted); }
        .v { color:var(--ink); font-weight:700; text-align:right; }
        .total-row { background: #f8fafc; padding: 12px; border-radius: 8px; margin-top: 12px; }
        .total-row .k { font-size: 15px; font-weight: 700; }
        .total-row .v { font-size: 18px; font-weight: 900; color: var(--brand); }

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
    $baseTotal = $subtotal + $shippingCost; // Harga produk + ongkir saja
    $platformFee = (int) ceil($baseTotal * ($paymentFeePercent / 100)); // Platform fee 5%
    $paymentMethodOptions = [
        ['code' => 'bank_transfer', 'label' => 'Transfer Bank', 'icon' => 'BANK', 'desc' => 'Rekomendasi awal. Metode akhir dipilih di halaman checkout Xendit', 'fee' => 0],
        ['code' => 'qris', 'label' => 'QRIS', 'icon' => 'QR', 'desc' => 'Rekomendasi awal. Metode akhir dipilih di halaman checkout Xendit', 'fee' => 0],
        ['code' => 'dana', 'label' => 'DANA', 'icon' => 'D', 'desc' => 'Rekomendasi awal. Metode akhir dipilih di halaman checkout Xendit', 'fee' => 0],
        ['code' => 'ovo', 'label' => 'OVO', 'icon' => 'O', 'desc' => 'Rekomendasi awal. Metode akhir dipilih di halaman checkout Xendit', 'fee' => 0],
        ['code' => 'linkaja', 'label' => 'LinkAja', 'icon' => 'L', 'desc' => 'Rekomendasi awal. Metode akhir dipilih di halaman checkout Xendit', 'fee' => 0],
        ['code' => 'retail', 'label' => 'Minimarket', 'icon' => 'M', 'desc' => 'Rekomendasi awal. Metode akhir dipilih di halaman checkout Xendit', 'fee' => 0],
    ];
@endphp

<div class="wrap">
    <div class="top">
        <a href="{{ route('checkout.checkpoint.show') }}" class="back">←</a>
        <div class="ttl">Pilih Metode Pembayaran</div>
    </div>

    <div class="hero">
        <h1>Pilih Cara Bayar</h1>
        <p>Pilih metode pembayaran yang paling sesuai dengan kebutuhan Anda.</p>
    </div>

    <div class="grid">
        <div class="card">
            <div class="hd">Metode Pembayaran</div>
            <div class="bd">
                <div class="payment-methods">
                    @foreach($paymentMethodOptions as $method)
                    @php($methodExtraFee = $platformFee + $method['fee'])
                    <label class="payment-method" data-method="{{ $method['code'] }}" data-fee="{{ $method['fee'] }}" data-label="{{ $method['label'] }}">
                        <input type="radio" name="payment_method" value="{{ $method['code'] }}" required>
                        <div class="method-header">
                            <div class="method-icon">{{ $method['icon'] }}</div>
                            <div class="method-info">
                                <div class="method-name">{{ $method['label'] }}</div>
                                <div class="method-desc">{{ $method['desc'] }}</div>
                            </div>
                        </div>
                        <div class="method-fee">Fee Sama</div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="hd">Ringkasan Pembayaran</div>
            <div class="bd">
                <div class="row"><span class="k">Harga Produk</span><span class="v">Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                <div class="row"><span class="k">Ongkir</span><span class="v">{{ $shippingEnabled ? 'Rp ' . number_format($shippingCost, 0, ',', '.') : 'Gratis Ongkir' }}</span></div>
                <div class="row subtotal-row"><span class="k">Subtotal Dasar</span><span class="v">Rp {{ number_format($baseTotal, 0, ',', '.') }}</span></div>
                <div class="row payment-fee-row" style="display: none;"><span class="k">Total Fee Pembayaran</span><span class="v payment-fee-amount">Rp 0</span></div>
                <div class="total-row row"><span class="k">Total Pembayaran</span><span class="v total-amount">Rp {{ number_format($baseTotal, 0, ',', '.') }}</span></div>

                <div class="note">
                    💡 <strong>Total akhir di halaman ini tidak memakai fee per metode.</strong> Ini sengaja supaya tidak ada celah pilih metode murah di awal lalu bayar dengan metode lain di halaman <strong>Xendit Hosted Checkout</strong>.
                </div>

                <div class="action">
                    <a href="{{ route('checkout.checkpoint.show') }}" class="btn btn-back">← Kembali</a>
                    <button type="button" id="btnPayNow" class="btn btn-pay" disabled>
                        <span class="spinner" id="paySpinner"></span>
                        <span id="payBtnTxt">Pilih Metode Dulu</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="paymentAlert" class="alert"></div>

<script>
const payload = @json($payload);
const baseTotal = {{ $baseTotal }};
const platformFee = {{ $platformFee }};
let selectedMethod = null;
let selectedFee = 0;
let selectedMethodLabel = '';

function updateTotal() {
    const combinedFee = platformFee + selectedFee;
    const total = baseTotal + combinedFee;
    document.querySelector('.total-amount').textContent = 'Rp ' + total.toLocaleString('id-ID');

    const feeAmountEl = document.querySelector('.payment-fee-amount');
    const feeRow = document.querySelector('.payment-fee-row');
    if (combinedFee > 0) {
        feeAmountEl.textContent = 'Rp ' + combinedFee.toLocaleString('id-ID');
        feeRow.style.display = 'flex';
    } else {
        feeRow.style.display = 'none';
    }
}

function selectPaymentMethod(methodElement) {
    // Remove selected class from all methods
    document.querySelectorAll('.payment-method').forEach(el => {
        el.classList.remove('selected');
    });

    // Add selected class to clicked method
    methodElement.classList.add('selected');

    // Update selected method and fee
    selectedMethod = methodElement.dataset.method;
    selectedFee = parseInt(methodElement.dataset.fee) || 0;
    selectedMethodLabel = methodElement.dataset.label || selectedMethod;

    // Update radio button
    methodElement.querySelector('input[type="radio"]').checked = true;

    // Update total
    updateTotal();

    // Enable pay button
    const payBtn = document.getElementById('btnPayNow');
    const payTxt = document.getElementById('payBtnTxt');
    payBtn.disabled = false;
    payTxt.textContent = 'Bayar Sekarang';
}

function showAlert(msg) {
    const el = document.getElementById('paymentAlert');
    if (!el) return;
    el.textContent = msg;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 3200);
}

// Add click handlers to payment methods
document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', () => selectPaymentMethod(method));
});

// Pay button handler
document.getElementById('btnPayNow').addEventListener('click', async function () {
    if (!selectedMethod) {
        showAlert('Pilih metode pembayaran terlebih dahulu.');
        return;
    }

    const btn = this;
    const spin = document.getElementById('paySpinner');
    const txt = document.getElementById('payBtnTxt');
    btn.disabled = true;
    spin.style.display = 'inline-block';
    txt.textContent = 'Memproses...';

    try {
        // Add payment method and fee to payload
        const paymentPayload = {
            ...payload,
            payment_method: selectedMethod,
            payment_method_fee: selectedFee,
            total_amount: baseTotal + platformFee + selectedFee
        };

        const res = await fetch('{{ route("checkout.createCharge") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(paymentPayload),
        });

        const result = await res.json();
        if (!res.ok || result.error) {
            throw new Error(result.message || 'Gagal memproses pembayaran.');
        }

        // Redirect to payment URL
        if (result.payment_url) {
            window.location.href = result.payment_url;
        } else {
            throw new Error('URL pembayaran tidak ditemukan.');
        }

    } catch (err) {
        showAlert(err.message || 'Terjadi kesalahan saat menghubungi server.');
        reset();
    }

    function reset() {
        btn.disabled = false;
        spin.style.display = 'none';
        txt.textContent = 'Bayar Sekarang';
    }
});
</script>
</body>
</html>
