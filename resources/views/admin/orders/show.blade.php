@extends('admin.layouts.app')
@section('page-title', 'Detail Order #' . str_pad($order['id'], 5, '0', STR_PAD_LEFT))

@push('styles')
<style>
.detail-grid { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }

.card { background: white; border-radius: 16px; border: 1.5px solid var(--line); overflow: hidden; margin-bottom: 16px; }
.card:last-child { margin-bottom: 0; }
.card-head { padding: 16px 18px; border-bottom: 1.5px solid var(--line); background: #f7f9ff; display: flex; justify-content: space-between; align-items: center; }
.card-title { font-size: 13.5px; font-weight: 800; color: var(--ink); }
.card-sub   { font-size: 11px; color: var(--ink4); font-weight: 600; margin-top: 1px; }
.card-body  { padding: 18px; }

.info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1.5px solid var(--line); }
.info-row:last-child { border-bottom: none; }
.info-lbl { font-size: 11.5px; color: var(--ink3); font-weight: 600; }
.info-val { font-size: 12.5px; font-weight: 700; color: var(--ink); }

.ucell-lg { display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-bottom: 1.5px solid var(--line); }
.ucell-lg:last-child { border-bottom: none; }
.uav-lg { width: 42px; height: 42px; border-radius: 11px; background: linear-gradient(135deg,#3b82f6,#6366f1); display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 900; color: white; flex-shrink: 0; }
.u-role { font-size: 10.5px; color: var(--ink4); font-weight: 700; letter-spacing: .5px; text-transform: uppercase; }
.u-name { font-size: 14px; font-weight: 800; color: var(--ink); margin-top: 1px; }
.u-link { font-size: 11.5px; color: var(--b500); font-family: var(--mono); }

.tl-wrap { padding: 18px; }
.tl-item { display: flex; gap: 12px; margin-bottom: 16px; }
.tl-item:last-child { margin-bottom: 0; }
.tl-dots { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
.tl-dot  { width: 10px; height: 10px; border-radius: 50%; margin-top: 3px; flex-shrink: 0; }
.tl-line { width: 2px; flex: 1; background: var(--line); margin-top: 4px; min-height: 18px; }
.tl-item:last-child .tl-line { display: none; }
.tl-title { font-size: 13px; font-weight: 700; color: var(--ink); }
.tl-time  { font-size: 11px; color: var(--ink4); margin-top: 2px; }

.action-card { background: white; border-radius: 16px; border: 1.5px solid var(--line); padding: 18px 20px; }
.action-title { font-size: 13px; font-weight: 800; color: var(--ink); margin-bottom: 12px; }
.action-full { width: 100%; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; border: 1.5px solid var(--line); background: white; color: var(--ink); margin-bottom: 8px; transition: all .12s; display: block; text-align: center; text-decoration: none; box-sizing: border-box; }
.action-full:last-child { margin-bottom: 0; }
.action-full:hover { background: #f0f4ff; }
.action-full.danger { color: #dc2626; border-color: #fecaca; }
.action-full.danger:hover { background: #fee2e2; }
.action-full.success { color: #16a34a; border-color: #bbf7d0; }
.action-full.success:hover { background: #dcfce7; }
.action-full.dispute { color: #7c3aed; border-color: #ddd6fe; }
.action-full.dispute:hover { background: #ede9fe; }

.dispute-box { background: #ede9fe; border: 1.5px solid #ddd6fe; border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; }
.dispute-title { font-size: 12.5px; font-weight: 800; color: #5b21b6; margin-bottom: 4px; }
.dispute-text  { font-size: 12px; color: #6d28d9; line-height: 1.6; }

.badge { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 7px; display: inline-block; }
.b-ok      { color: #16a34a; background: #dcfce7; }
.b-off     { color: #dc2626; background: #fee2e2; }
.b-warn    { color: #d97706; background: #fef3c7; }
.b-dispute { color: #7c3aed; background: #ede9fe; }
.num { font-family: var(--mono); font-size: 13px; font-weight: 700; }
</style>
@endpush

@section('content')
<div style="margin-bottom:16px;">
  <a href="{{ route('admin.orders.index') }}" style="font-size:13px;font-weight:700;color:var(--b500);text-decoration:none;">← Kembali ke Order</a>
</div>

<div class="detail-grid">

  {{-- KIRI --}}
  <div>
    @if($order['status'] === 'dispute')
    <div class="dispute-box">
      <div class="dispute-title">⚠️ Order ini sedang dalam dispute</div>
      <div class="dispute-text">{{ $order['dispute_reason'] ?? 'Pembeli mengajukan komplain. Silakan tinjau dan ambil tindakan.' }}</div>
    </div>
    @endif

    {{-- INFO ORDER --}}
    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">Info Order</div>
          <div class="card-sub">#{{ str_pad($order['id'], 5, '0', STR_PAD_LEFT) }}</div>
        </div>
        @if($order['status'] === 'paid')        <span class="badge b-ok">Berhasil</span>
        @elseif($order['status'] === 'pending')  <span class="badge b-warn">Pending</span>
        @elseif($order['status'] === 'dispute')  <span class="badge b-dispute">Dispute</span>
        @elseif($order['status'] === 'failed')   <span class="badge b-off">Gagal</span>
        @elseif($order['status'] === 'refunded') <span class="badge b-off">Refunded</span>
        @endif
      </div>
      <div class="card-body">
        <div class="info-row"><span class="info-lbl">Kode Order</span><span class="info-val num">{{ $order['order_code'] }}</span></div>
        <div class="info-row"><span class="info-lbl">Produk</span><span class="info-val">{{ $order['product_name'] }}</span></div>
        <div class="info-row"><span class="info-lbl">Tipe</span><span class="info-val">{{ $order['product_type'] === 'digital' ? 'Digital' : 'Fisik' }}</span></div>
        <div class="info-row"><span class="info-lbl">Subtotal</span><span class="info-val num">Rp {{ number_format($order['subtotal'], 0, ',', '.') }}</span></div>
        <div class="info-row"><span class="info-lbl">Biaya Platform</span><span class="info-val num">Rp {{ number_format($order['platform_fee'], 0, ',', '.') }}</span></div>
        <div class="info-row">
          <span class="info-lbl">Total Dibayar</span>
          <span class="info-val num" style="font-size:15px;color:var(--b500);">Rp {{ number_format($order['total'], 0, ',', '.') }}</span>
        </div>
        <div class="info-row"><span class="info-lbl">Metode Bayar</span><span class="info-val">{{ ucfirst($order['payment_method']) }}</span></div>
        <div class="info-row"><span class="info-lbl">Tanggal Order</span><span class="info-val">{{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y, H:i') }}</span></div>
        @if($order['paid_at'])
        <div class="info-row"><span class="info-lbl">Tanggal Bayar</span><span class="info-val">{{ \Carbon\Carbon::parse($order['paid_at'])->format('d M Y, H:i') }}</span></div>
        @endif
      </div>
    </div>

    {{-- PIHAK TERLIBAT --}}
    <div class="card">
      <div class="card-head"><div class="card-title">Pihak Terlibat</div></div>

      {{-- Pembeli — tidak ada user ID, tidak ada link profil untuk digital --}}
      <div class="ucell-lg">
        <div class="uav-lg">{{ $order['buyer_initials'] }}</div>
        <div style="flex:1;">
          <div class="u-role">Pembeli</div>
          <div class="u-name">{{ $order['buyer_name'] }}</div>
          <div class="u-link">{{ $order['buyer_email'] }}</div>
        </div>
      </div>

      {{-- Seller --}}
      <div class="ucell-lg">
        <div class="uav-lg" style="background:linear-gradient(135deg,#10b981,#059669);">{{ $order['seller_initials'] }}</div>
        <div style="flex:1;">
          <div class="u-role">Seller</div>
          <div class="u-name">{{ $order['seller_name'] }}</div>
          <div class="u-link">{{ $order['seller_email'] }}</div>
        </div>
        @if($order['seller_id'])
          <a href="{{ route('admin.users.show', $order['seller_id']) }}" style="font-size:12px;font-weight:700;color:var(--b500);text-decoration:none;flex-shrink:0;">Lihat profil →</a>
        @endif
      </div>
    </div>

    {{-- TIMELINE --}}
    <div class="card">
      <div class="card-head"><div class="card-title">Timeline Order</div></div>
      <div class="tl-wrap">
        <div class="tl-item">
          <div class="tl-dots"><div class="tl-dot" style="background:#3b82f6;"></div><div class="tl-line"></div></div>
          <div><div class="tl-title">Order dibuat</div><div class="tl-time">{{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y, H:i') }}</div></div>
        </div>
        @if($order['paid_at'])
        <div class="tl-item">
          <div class="tl-dots"><div class="tl-dot" style="background:#16a34a;"></div><div class="tl-line"></div></div>
          <div><div class="tl-title">Pembayaran berhasil</div><div class="tl-time">{{ \Carbon\Carbon::parse($order['paid_at'])->format('d M Y, H:i') }}</div></div>
        </div>
        @endif
        @if($order['status'] === 'dispute' && $order['dispute_at'])
        <div class="tl-item">
          <div class="tl-dots"><div class="tl-dot" style="background:#7c3aed;"></div><div class="tl-line"></div></div>
          <div><div class="tl-title">Dispute diajukan</div><div class="tl-time">{{ \Carbon\Carbon::parse($order['dispute_at'])->format('d M Y, H:i') }}</div></div>
        </div>
        @endif
        @if($order['refunded_at'])
        <div class="tl-item">
          <div class="tl-dots"><div class="tl-dot" style="background:#dc2626;"></div><div class="tl-line"></div></div>
          <div><div class="tl-title">Refund diproses</div><div class="tl-time">{{ \Carbon\Carbon::parse($order['refunded_at'])->format('d M Y, H:i') }}</div></div>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- KANAN --}}
  <div>
    <div class="action-card">
      <div class="action-title">Aksi Admin</div>

      <a href="mailto:{{ $order['buyer_email'] }}" class="action-full">✉️ Hubungi Pembeli</a>
      <a href="mailto:{{ $order['seller_email'] }}" class="action-full">✉️ Hubungi Seller</a>
    </div>
  </div>

</div>
@endsection