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
        /* AUTOCOMPLETE */
        .city-wrap { position:relative; }
        .city-dd { position:absolute; top:calc(100% + 4px); left:0; right:0; background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.10); z-index:100; max-height:240px; overflow-y:auto; display:none; }
        .city-dd.show { display:block; }
        .city-item { padding:10px 14px; font-size:13px; color:#374151; cursor:pointer; border-bottom:1px solid #f3f4f6; line-height:1.4; }
        .city-item:last-child { border-bottom:none; }
        .city-item:hover, .city-item.active { background:#eff6ff; color:#2563eb; }
        .city-item .city-main { font-weight:600; }
        .city-item .city-sub { font-size:11px; color:#6b7280; }
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
        .ongkir-badge { font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; background:#e0e7ff; color:#4338ca; flex-shrink:0; }
        .ongkir-name { font-size:13px; font-weight:600; color:#111827; flex:1; }
        .ongkir-etd { font-size:11px; color:#6b7280; }
        .ongkir-price { font-size:14px; font-weight:700; color:#2563eb; }
        @keyframes spin { to { transform:rotate(360deg); } }
        /* SUMMARY */
        .sum-row { display:flex; justify-content:space-between; align-items:center; font-size:14px; color:#374151; padding:6px 0; }
        .sum-row.total { font-weight:700; font-size:16px; color:#111827; padding-top:12px; margin-top:6px; border-top:1px solid #e5e7eb; }
        .sum-row.total .total-amt { color:#2563eb; font-size:18px; }
        .sum-ph { color:#9ca3af; font-style:italic; font-size:12px; }
        .btn-pay { width:100%; max-width:500px; margin:0 auto; display:block; background:#2563eb; color:#fff; border:none; border-radius:10px; padding:14px; font-size:15px; font-weight:700; cursor:pointer; transition:background .2s; }
        .btn-pay:hover:not(:disabled) { background:#1d4ed8; }
        .btn-pay:disabled { background:#93c5fd; cursor:not-allowed; }
        .spinner { display:none; width:16px; height:16px; border:2px solid rgba(255,255,255,.4); border-top-color:#fff; border-radius:50%; animation:spin .7s linear infinite; margin-right:8px; vertical-align:middle; }
        .alert-info { max-width:500px; margin:0 auto 16px; padding:12px 14px; border-radius:8px; font-size:13px; display:flex; align-items:flex-start; gap:8px; background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; }
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
<div class="alert-info">
    <span>📦</span><span>Produk digital — file dikirim ke email Anda setelah pembayaran berhasil.</span>
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
                    <div style="margin-top:5px;font-size:11px;color:#6b7280;">⚖️ {{ $product->weight }}gr</div>
                @endif
            </div>
        </div>
        @if($seller)
        <div class="seller-row">
            @if($seller->avatar ?? null)
                <img src="{{ asset('storage/'.$seller->avatar) }}" class="seller-ava" alt="{{ $seller->name }}">
            @else
                <div class="seller-ava-ph">👤</div>
            @endif
            <div style="font-size:13px;color:#374151;">
                Dijual oleh <strong>{{ $seller->name }}</strong>
                @if($seller->origin_city_name ?? null)
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
                <input type="text" name="buyer_name" value="{{ auth()->user()->name ?? '' }}" placeholder="Nama lengkap" required>
            </div>
            <div class="form-group">
                <label>Email <span class="req">*</span></label>
                <input type="email" name="buyer_email" value="{{ auth()->user()->email ?? '' }}" placeholder="email@contoh.com" required>
                @if($product->product_type === 'digital')
                    <div class="hint">📧 File digital dikirim ke email ini.</div>
                @endif
            </div>
            <div class="form-group">
                <label>No. WhatsApp <span class="req">*</span></label>
                <input type="tel" name="buyer_phone" placeholder="08xxxxxxxxxx" required>
            </div>

            @if($product->product_type === 'fisik')

            <div class="form-group">
                <label>Alamat Lengkap <span class="req">*</span></label>
                <textarea name="buyer_address" placeholder="Nama jalan, nomor rumah, RT/RW..." required></textarea>
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
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 12h14"/></svg>
                        </button>
                        <input type="number" id="qtyDisplay" class="qty-input" value="1" min="1" max="{{ $maxQty }}" inputmode="numeric">
                        <button type="button" class="qty-btn" id="qtyPlus" onclick="changeQty(1)">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>
                </div>
                <input type="number" name="qty" id="qtyHidden" value="1" min="1" max="{{ $maxQty }}" style="display:none">
            </div>

            <input type="hidden" name="selected_courier" id="selCourier" value="FLAT">
            <input type="hidden" name="selected_service" id="selService" value="Flat Shipping">
            <input type="hidden" name="selected_ongkir_cost" id="selCost" value="{{ (int) ($product->shipping_cost ?? 0) }}">

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
            <div class="sum-row"><span>Jumlah</span><span id="sumQty">1</span></div>
            <div class="sum-row"><span>Subtotal</span><span id="sumSubtotal">Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}</span></div>
            <div class="sum-row">
                <span>Ongkos Kirim Flat</span>
                <span id="sumShipping">Rp {{ number_format((int) ($product->shipping_cost ?? 0), 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="sum-row total">
                <span>Total Pembayaran</span>
                <span class="total-amt" id="sumTotal">Rp {{ number_format((int) (($product->discount ?? $product->price) + ($product->product_type === 'fisik' ? ($product->shipping_cost ?? 0) : 0)), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <button type="submit" class="btn-pay" id="btnPay">
        <span class="spinner" id="paySpinner"></span>
        <span id="btnPayText">Bayar Sekarang</span>
    </button>
</form>

<div class="secure">🔒 Pembayaran aman & terenkripsi via Midtrans</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
const UNIT_PRICE           = {{ $product->discount ?? $product->price ?? 0 }};
const MAX_QTY              = {{ $product->purchase_limit ?? ($product->stock ?? 99) }};
const PRODUCT_TYPE         = '{{ $product->product_type }}';
const FLAT_SHIPPING        = {{ $product->product_type === 'fisik' ? (int) ($product->shipping_cost ?? 0) : 0 }};

let currentQty   = 1;
let selectedCost = FLAT_SHIPPING;

function fmtRp(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n); }

// ===== SUMMARY =====
function updateSummary(qty) {
    qty = Math.min(Math.max(qty, 1), MAX_QTY);
    const sub = UNIT_PRICE * qty;
    const tot = sub + (PRODUCT_TYPE === 'fisik' ? FLAT_SHIPPING : 0);
    const elQ=document.getElementById('sumQty'), elS=document.getElementById('sumSubtotal'), elT=document.getElementById('sumTotal');
    if(elQ) elQ.textContent = qty;
    if(elS) elS.textContent = fmtRp(sub);
    const elShip = document.getElementById('sumShipping');
    if(elShip && PRODUCT_TYPE === 'fisik') elShip.textContent = fmtRp(FLAT_SHIPPING);
    if(elT) elT.textContent = fmtRp(tot);
}

// ===== QTY =====
const qtyDisplay=document.getElementById('qtyDisplay'), qtyHidden=document.getElementById('qtyHidden');
const qtyMinus=document.getElementById('qtyMinus'), qtyPlus=document.getElementById('qtyPlus');
function applyQty(n){
    if(isNaN(n)||n<1) n=1; if(n>MAX_QTY) n=MAX_QTY;
    currentQty=n;
    if(qtyDisplay) qtyDisplay.value=n;
    if(qtyHidden)  qtyHidden.value=n;
    if(qtyMinus)   qtyMinus.disabled=n<=1;
    if(qtyPlus)    qtyPlus.disabled=n>=MAX_QTY;
    updateSummary(n);
}
function changeQty(d){ applyQty(currentQty+d); }
if(qtyDisplay){
    qtyDisplay.addEventListener('input',function(){ const v=parseInt(this.value,10); if(!isNaN(v))applyQty(v); });
    qtyDisplay.addEventListener('blur',function(){ applyQty(parseInt(this.value,10)); });
    qtyDisplay.addEventListener('keydown',function(e){ if(e.key==='Enter'){e.preventDefault();this.blur();} if(!['Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Tab','Home','End'].includes(e.key)&&!/^[0-9]$/.test(e.key))e.preventDefault(); });
}

// ===== SUBMIT =====
document.getElementById('checkoutForm').addEventListener('submit',async function(e){
    e.preventDefault();
    applyQty(parseInt(qtyDisplay?qtyDisplay.value:1,10));
    const btn=document.getElementById('btnPay'), spinner=document.getElementById('paySpinner'), btnTxt=document.getElementById('btnPayText');
    btn.disabled=true; spinner.style.display='inline-block'; btnTxt.textContent='Memproses...';
    const data=Object.fromEntries(new FormData(this).entries());
    try{
        const res=await fetch('{{ route("checkout.process") }}',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},
            body:JSON.stringify({product_id:{{ $product->id }},...data}),
        });
        const result=await res.json();
        if(!res.ok||result.error) throw new Error(result.message||'Terjadi kesalahan.');
        snap.pay(result.snap_token,{
            onSuccess:()=>window.location.href='{{ route("checkout.success") }}?order_id='+result.order_id,
            onPending:()=>window.location.href='{{ route("checkout.pending") }}?order_id='+result.order_id,
            onError:()=>{ alert('Pembayaran gagal. Coba lagi.'); resetBtn(); },
            onClose:()=>resetBtn(),
        });
    } catch(err){ alert(err.message||'Gagal menghubungi server.'); resetBtn(); }
    function resetBtn(){ btn.disabled=false; spinner.style.display='none'; btnTxt.textContent='Bayar Sekarang'; }
});
</script>
</body>
</html>
