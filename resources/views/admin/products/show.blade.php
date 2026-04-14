@extends('admin.layouts.app')
@section('page-title', 'Detail Produk — ' . $product->title)

@push('styles')
<style>
:root {
  --r: #dc2626; --r-bg: #fef2f2;
  --g: #16a34a; --g-bg: #f0fdf4;
  --b: #2563eb; --b-bg: #eff6ff;
  --a: #d97706; --a-bg: #fffbeb;
  --v: #7c3aed; --v-bg: #f5f3ff;
  --ink0: var(--ink,#0f172a);
  --ink2: #475569; --ink3: #94a3b8;
  --card-bg: #ffffff;
  --line-c: #e2e8f0;
  --hover-bg: #f8fafc;
  --transition: .18s cubic-bezier(.4,0,.2,1);
}

/* ── Back link ── */
.back-link {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; font-weight: 700;
  color: var(--b); text-decoration: none;
  margin-bottom: 20px;
  transition: gap var(--transition), opacity var(--transition);
}
.back-link:hover { gap: 10px; opacity: .8; }

/* ── Grid ── */
.detail-grid {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 20px;
  align-items: start;
}

/* ── Card base ── */
.card {
  background: var(--card-bg);
  border-radius: 16px;
  border: 1px solid var(--line-c);
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
  transition: box-shadow var(--transition);
}

/* ── Info card (left) ── */
.info-head {
  padding: 14px 18px;
  border-bottom: 1px solid var(--line-c);
  background: #f8fafc;
  display: flex; align-items: center; gap: 8px;
}
.info-head-title { font-size: 13px; font-weight: 800; color: var(--ink0); }
.info-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 12px 18px; border-bottom: 1px solid var(--line-c);
  transition: background var(--transition);
}
.info-row:last-child { border-bottom: none; }
.info-row:hover { background: var(--hover-bg); }
.info-lbl { font-size: 11.5px; color: var(--ink3); font-weight: 600; }
.info-val { font-size: 12.5px; font-weight: 700; color: var(--ink0); text-align: right; max-width: 60%; }

/* ── Seller card ── */
.seller-card {
  background: var(--card-bg);
  border-radius: 14px;
  border: 1px solid var(--line-c);
  padding: 14px 16px;
  margin-top: 14px;
  display: flex; align-items: center; gap: 12px;
  transition: box-shadow var(--transition), border-color var(--transition);
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.seller-card:hover {
  box-shadow: 0 6px 20px rgba(0,0,0,.07);
  border-color: #cbd5e1;
}
.seller-av {
  width: 42px; height: 42px; border-radius: 11px;
  background: linear-gradient(135deg,#3b82f6,#6366f1);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 900; color: white;
  flex-shrink: 0; letter-spacing: -.5px;
}
.seller-lbl  { font-size: 10px; color: var(--ink3); font-weight: 700; text-transform: uppercase; letter-spacing: .6px; }
.seller-name { font-size: 14px; font-weight: 800; color: var(--ink0); margin: 1px 0; }
.seller-slug { font-size: 11.5px; color: var(--b); font-family: var(--mono, monospace); }

/* ── Right column ── */
.right-col { display: flex; flex-direction: column; gap: 16px; }

/* ── Stats row ── */
.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}
.stat-mini {
  background: var(--card-bg);
  border-radius: 14px;
  border: 1px solid var(--line-c);
  padding: 16px 18px;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
  transition: box-shadow var(--transition), transform var(--transition);
}
.stat-mini:hover {
  box-shadow: 0 8px 24px rgba(0,0,0,.07);
  transform: translateY(-2px);
}
.stat-mini-icon {
  width: 32px; height: 32px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 10px;
}
.stat-mini-val { font-size: 20px; font-weight: 800; color: var(--ink0); letter-spacing: -.5px; line-height: 1; }
.stat-mini-lbl { font-size: 11px; color: var(--ink3); font-weight: 600; margin-top: 4px; }

/* ── Table card ── */
.card-head {
  padding: 14px 18px;
  border-bottom: 1px solid var(--line-c);
  background: #f8fafc;
  display: flex; justify-content: space-between; align-items: center;
}
.card-title { font-size: 13px; font-weight: 800; color: var(--ink0); }
.card-sub   { font-size: 11px; color: var(--ink3); font-weight: 600; margin-top: 2px; }

table { width: 100%; border-collapse: collapse; }
th {
  font-size: 10.5px; font-weight: 800; color: var(--ink3);
  text-align: left; padding: 11px 18px;
  letter-spacing: .6px; text-transform: uppercase;
  border-bottom: 1px solid var(--line-c);
}
td {
  font-size: 13px; color: var(--ink0);
  padding: 13px 18px;
  border-bottom: 1px solid var(--line-c);
  vertical-align: middle;
}
tr:last-child td { border-bottom: none; }
tbody tr { transition: background var(--transition); }
tbody tr:hover td { background: var(--hover-bg); }

/* ── Buyer cell ── */
.buyer-name  { font-size: 13px; font-weight: 700; color: var(--ink0); }
.buyer-email { font-size: 11px; color: var(--ink3); font-family: var(--mono, monospace); }

/* ── Type badge ── */
.type-tag {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 11.5px; font-weight: 700;
  padding: 4px 10px; border-radius: 20px;
}
.type-digital  { color: var(--b); background: var(--b-bg); }
.type-physical { color: var(--g); background: var(--g-bg); }

/* ── Status badge ── */
.badge {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 700;
  padding: 3px 10px; border-radius: 20px;
}
.b-ok   { color: var(--g); background: var(--g-bg); }
.b-off  { color: var(--r); background: var(--r-bg); }
.b-warn { color: var(--a); background: var(--a-bg); }
.b-ship { color: var(--v); background: var(--v-bg); }

/* ── Price ── */
.price-original   { font-size: 11px; color: var(--ink3); text-decoration: line-through; }
.price-discounted { font-size: 13px; font-weight: 800; color: var(--ink0); }
.discount-badge   { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 2px 7px; border-radius: 5px; color: var(--r); background: var(--r-bg); margin-left: 4px; }
.num { font-family: var(--mono, monospace); font-size: 13px; font-weight: 700; }

/* ── Order code ── */
.order-code {
  font-family: var(--mono, monospace); font-size: 12px; font-weight: 700;
  color: var(--ink2); background: var(--hover-bg);
  padding: 3px 8px; border-radius: 6px;
  border: 1px solid var(--line-c);
}

/* ── Date cell ── */
.date-cell { font-size: 12px; color: var(--ink3); }

/* ── Empty ── */
.empty-order { padding: 48px 20px; text-align: center; color: var(--ink3); font-weight: 600; font-size: 13px; }
</style>
@endpush

@section('content')

<a href="{{ route('admin.products.index') }}" class="back-link">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="15 18 9 12 15 6"/>
  </svg>
  Kembali ke Toko &amp; Produk
</a>

<div class="detail-grid">

  {{-- ══ KIRI ══ --}}
  <div>

    {{-- Info card --}}
    <div class="card">
      <div class="info-head">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--b)">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
        </svg>
        <div class="info-head-title">Info Produk</div>
      </div>

      <div class="info-row">
        <span class="info-lbl">Nama</span>
        <span class="info-val">{{ $product->title }}</span>
      </div>

      <div class="info-row">
        <span class="info-lbl">Tipe</span>
        <span class="info-val">
          @if($product->product_type === 'fisik')
            <span class="type-tag type-physical">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              Fisik
            </span>
          @else
            <span class="type-tag type-digital">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              Digital
            </span>
          @endif
        </span>
      </div>

      {{-- Harga --}}
      @if($discountPercent !== null)
        <div class="info-row">
          <span class="info-lbl">Harga</span>
          <span class="info-val">
            <span class="price-original">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            <span class="discount-badge">-{{ $discountPercent }}%</span>
            <br>
            <span class="price-discounted">Rp {{ number_format($product->discount, 0, ',', '.') }}</span>
          </span>
        </div>
      @else
        <div class="info-row">
          <span class="info-lbl">Harga</span>
          <span class="info-val">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
        </div>
      @endif

      @if($product->product_type === 'fisik')
        <div class="info-row">
          <span class="info-lbl">Stok</span>
          <span class="info-val">{{ number_format($product->stock ?? 0) }} unit</span>
        </div>
        <div class="info-row">
          <span class="info-lbl">Berat</span>
          <span class="info-val">{{ $product->weight ?? '-' }} gram</span>
        </div>
        <div class="info-row">
          <span class="info-lbl">Ongkir</span>
          <span class="info-val">
            @if($product->shipping_enabled)
              <span class="badge b-ok">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                Aktif
              </span>
            @else
              <span class="badge b-off">Nonaktif</span>
            @endif
          </span>
        </div>
      @endif

      <div class="info-row">
        <span class="info-lbl">Dibuat</span>
        <span class="info-val">{{ $product->created_at->format('d M Y') }}</span>
      </div>
    </div>

    {{-- Seller card --}}
    <div class="seller-card">
      <div class="seller-av">{{ strtoupper(substr($product->owner->name ?? 'U', 0, 2)) }}</div>
      <div>
        <div class="seller-lbl">Seller</div>
        <div class="seller-name">{{ $product->owner->name ?? '-' }}</div>
        <div class="seller-slug">mobay.id/{{ $product->owner->username ?? '-' }}</div>
      </div>
    </div>

  </div>

  {{-- ══ KANAN ══ --}}
  <div class="right-col">

    {{-- Stats mini --}}
    <div class="stats-row">
      <div class="stat-mini">
        <div class="stat-mini-icon" style="background:var(--b-bg);color:var(--b)">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
        <div class="stat-mini-val">{{ number_format($stats['total_sales']) }}</div>
        <div class="stat-mini-lbl">Total Terjual</div>
      </div>
      <div class="stat-mini">
        <div class="stat-mini-icon" style="background:var(--g-bg);color:var(--g)">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="12" y1="1" x2="12" y2="23"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </div>
        <div class="stat-mini-val" style="font-size:16px">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
        <div class="stat-mini-lbl">Total Revenue</div>
      </div>
      <div class="stat-mini">
        <div class="stat-mini-icon" style="background:var(--a-bg);color:var(--a)">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </div>
        <div class="stat-mini-val">{{ number_format($stats['total_views']) }}</div>
        <div class="stat-mini-lbl">Total Views</div>
      </div>
    </div>

    {{-- Digital orders --}}
    @if($digitalOrders->count() > 0)
    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">Riwayat Order Digital</div>
          <div class="card-sub">{{ $digitalOrders->count() }} order</div>
        </div>
        <span class="type-tag type-digital">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          Digital
        </span>
      </div>
      <table>
        <thead>
          <tr>
            <th>Kode Order</th>
            <th>Pembeli</th>
            <th>Total</th>
            <th>Status</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($digitalOrders as $order)
          <tr>
            <td><span class="order-code">{{ $order->order_code }}</span></td>
            <td>
              <div class="buyer-name">{{ $order->buyer_name }}</div>
              <div class="buyer-email">{{ $order->buyer_email }}</div>
            </td>
            <td class="num">Rp {{ number_format($order->amount, 0, ',', '.') }}</td>
            <td>
              @if($order->status === 'paid')
                <span class="badge b-ok">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                  Berhasil
                </span>
              @elseif($order->status === 'pending')
                <span class="badge b-warn">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  Pending
                </span>
              @else
                <span class="badge b-off">{{ ucfirst($order->status) }}</span>
              @endif
            </td>
            <td class="date-cell">{{ $order->created_at->format('d M Y') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

    {{-- Physical orders --}}
    @if($physicalOrders->count() > 0)
    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">Riwayat Order Fisik</div>
          <div class="card-sub">{{ $physicalOrders->count() }} order</div>
        </div>
        <span class="type-tag type-physical">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Fisik
        </span>
      </div>
      <table>
        <thead>
          <tr>
            <th>Kode Order</th>
            <th>Pembeli</th>
            <th>Total</th>
            <th>Status</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($physicalOrders as $order)
          <tr>
            <td><span class="order-code">{{ $order->order_code }}</span></td>
            <td>
              <div class="buyer-name">{{ $order->buyer_name }}</div>
              <div class="buyer-email">{{ $order->buyer_email }}</div>
            </td>
            <td class="num">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            <td>
              @if($order->status === 'paid')
                <span class="badge b-ok">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                  Dibayar
                </span>
              @elseif($order->status === 'pending')
                <span class="badge b-warn">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  Pending
                </span>
              @elseif($order->status === 'shipped')
                <span class="badge b-ship">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                  Dikirim
                </span>
              @elseif($order->status === 'delivered')
                <span class="badge b-ok">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                  Diterima
                </span>
              @elseif($order->status === 'cancelled')
                <span class="badge b-off">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  Dibatalkan
                </span>
              @else
                <span class="badge b-off">{{ ucfirst($order->status) }}</span>
              @endif
            </td>
            <td class="date-cell">{{ $order->created_at->format('d M Y') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

    {{-- Empty --}}
    @if($digitalOrders->count() === 0 && $physicalOrders->count() === 0)
    <div class="card">
      <div class="empty-order">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 12px;opacity:.3">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        Belum ada order untuk produk ini
      </div>
    </div>
    @endif

  </div>
</div>
@endsection