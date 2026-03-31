@extends('admin.layouts.app')
@section('page-title', 'Order')

@push('styles')
<style>
.top-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
.stat-card { background: white; border-radius: 16px; border: 1.5px solid var(--line); padding: 18px 20px; display: flex; flex-direction: column; gap: 6px; }
.stat-icon { font-size: 22px; margin-bottom: 2px; }
.stat-val  { font-size: 26px; font-weight: 900; color: var(--ink); letter-spacing: -.5px; }
.stat-lbl  { font-size: 11.5px; color: var(--ink3); font-weight: 600; }
.stat-tag  { display: inline-flex; font-size: 10.5px; font-weight: 700; padding: 3px 9px; border-radius: 8px; width: fit-content; }
.tup  { color: #16a34a; background: #dcfce7; }
.tdown{ color: #dc2626; background: #fee2e2; }
.tneu { color: var(--b500); background: #eff3ff; }

.filter-bar { display: flex; gap: 10px; align-items: center; margin-bottom: 16px; flex-wrap: wrap; }
.search-wrap { display: flex; align-items: center; gap: 8px; background: white; border: 1.5px solid var(--line); border-radius: 10px; padding: 8px 14px; flex: 1; min-width: 200px; }
.search-wrap input { border: none; outline: none; background: none; font-size: 13px; color: var(--ink); width: 100%; }
.fselect { border: 1.5px solid var(--line); border-radius: 10px; padding: 8px 14px; font-size: 13px; font-weight: 600; background: white; color: var(--ink); cursor: pointer; outline: none; }
.btn-export { display: flex; align-items: center; gap: 6px; border: 1.5px solid var(--line); background: white; border-radius: 10px; padding: 8px 16px; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--ink); text-decoration: none; }
.btn-export:hover { background: #f0f4ff; }

.table-card { background: white; border-radius: 16px; border: 1.5px solid var(--line); overflow: hidden; }
table { width: 100%; border-collapse: collapse; }
thead { background: #f7f9ff; }
th { font-size: 11px; font-weight: 800; color: var(--ink3); text-align: left; padding: 12px 16px; letter-spacing: .6px; text-transform: uppercase; border-bottom: 1.5px solid var(--line); }
td { font-size: 13px; color: var(--ink); padding: 13px 16px; border-bottom: 1.5px solid var(--line); vertical-align: middle; }
tr:last-child td { border-bottom: none; }
tr:hover td { background: #f7f9ff; }

.order-id   { font-family: var(--mono); font-size: 12.5px; font-weight: 700; }
.order-date { font-size: 11px; color: var(--ink4); margin-top: 1px; }
.prod-name  { font-size: 13px; font-weight: 700; }
.prod-meta  { font-size: 11px; color: var(--ink4); }

.ucell { display: flex; align-items: center; gap: 9px; }
.uav { width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg,#3b82f6,#6366f1); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 900; color: white; flex-shrink: 0; }
.uname { font-size: 13px; font-weight: 700; }
.uslug { font-size: 11px; color: var(--ink4); font-family: var(--mono); }

.badge { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 7px; display: inline-block; }
.b-ok      { color: #16a34a; background: #dcfce7; }
.b-off     { color: #dc2626; background: #fee2e2; }
.b-warn    { color: #d97706; background: #fef3c7; }
.b-dispute { color: #7c3aed; background: #ede9fe; }

.price-val { font-size: 13px; font-weight: 800; font-family: var(--mono); }

.action-wrap { display: flex; gap: 6px; }
.act-btn { border: 1.5px solid var(--line); border-radius: 7px; padding: 4px 11px; font-size: 11.5px; font-weight: 700; cursor: pointer; background: white; color: var(--ink); transition: all .12s; text-decoration: none; display: inline-block; }
.act-btn:hover { background: #f0f4ff; border-color: #c7d2fe; }
.act-btn.danger  { color: #dc2626; border-color: #fecaca; }
.act-btn.danger:hover  { background: #fee2e2; }
.act-btn.dispute { color: #7c3aed; border-color: #ddd6fe; }
.act-btn.dispute:hover { background: #ede9fe; }

.pag-wrap { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-top: 1.5px solid var(--line); }
.pag-info { font-size: 12px; color: var(--ink3); font-weight: 600; }
.pag-btns { display: flex; gap: 4px; }
.pag-btn { border: 1.5px solid var(--line); border-radius: 8px; padding: 5px 11px; font-size: 12px; font-weight: 700; cursor: pointer; background: white; color: var(--ink); text-decoration: none; display: inline-block; }
.pag-btn.active { background: var(--b500); color: white; border-color: var(--b500); }
.pag-btn:hover:not(.active) { background: #f0f4ff; }
button.pag-btn:disabled { opacity: .4; cursor: not-allowed; }
</style>
@endpush

@section('content')
<div style="display:flex;flex-direction:column;gap:0;">

  {{-- STAT CARDS --}}
  <div class="top-grid">
    <div class="stat-card">
      <div class="stat-icon">🧾</div>
      <div class="stat-val">{{ number_format($stats['total_orders']) }}</div>
      <div class="stat-lbl">Total Order</div>
      <span class="stat-tag tup">+{{ $stats['new_orders_today'] }} hari ini</span>
    </div>
    <div class="stat-card">
      <div class="stat-icon">✅</div>
      <div class="stat-val">{{ number_format($stats['success_orders']) }}</div>
      <div class="stat-lbl">Berhasil</div>
      <span class="stat-tag tup">{{ $stats['success_percent'] }}%</span>
    </div>
    <div class="stat-card">
      <div class="stat-icon">⏳</div>
      <div class="stat-val">{{ $stats['pending_orders'] }}</div>
      <div class="stat-lbl">Pending</div>
      @if($stats['pending_orders'] > 0)
        <span class="stat-tag tdown">Perlu perhatian</span>
      @else
        <span class="stat-tag tup">Bersih</span>
      @endif
    </div>
    <div class="stat-card">
      <div class="stat-icon">⚠️</div>
      <div class="stat-val">{{ $stats['dispute_orders'] }}</div>
      <div class="stat-lbl">Dispute Aktif</div>
      @if($stats['dispute_orders'] > 0)
        <span class="stat-tag tdown">Segera tangani</span>
      @else
        <span class="stat-tag tup">Aman</span>
      @endif
    </div>
  </div>

  {{-- FILTER --}}
  <div class="filter-bar">
    <div class="search-wrap">
      <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
        <circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.8"/>
        <path d="M10.5 10.5L14 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      </svg>
      <input type="text" id="searchInput" placeholder="Cari ID order atau nama pembeli..."
        value="{{ request('search') }}" onchange="applyFilter()"/>
    </div>
    <select class="fselect" id="typeFilter" onchange="applyFilter()">
      <option value="">Semua Tipe</option>
      <option value="digital"  {{ request('type') === 'digital'  ? 'selected' : '' }}>Digital</option>
      <option value="physical" {{ request('type') === 'physical' ? 'selected' : '' }}>Fisik</option>
    </select>
    <select class="fselect" id="statusFilter" onchange="applyFilter()">
      <option value="">Semua Status</option>
      <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>Berhasil</option>
      <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
      <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>Gagal</option>
      <option value="dispute" {{ request('status') === 'dispute' ? 'selected' : '' }}>Dispute</option>
    </select>
    <a href="{{ route('admin.orders.export', request()->all()) }}" class="btn-export">📥 Export CSV</a>
  </div>

  {{-- TABLE --}}
  <div class="table-card">
    <table>
      <thead>
        <tr>
          <th>ID Order</th>
          <th>Produk</th>
          <th>Pembeli</th>
          <th>Seller</th>
          <th>Total</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
        <tr>
          <td>
            <div class="order-id">#{{ str_pad($order['id'], 5, '0', STR_PAD_LEFT) }}</div>
            <div class="order-date">{{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y, H:i') }}</div>
          </td>
          <td>
            <div class="prod-name">{{ Str::limit($order['product_name'], 28) }}</div>
            <div class="prod-meta">{{ $order['product_type'] === 'digital' ? 'Digital' : 'Fisik' }}</div>
          </td>
          <td>
            <div class="ucell">
              <div class="uav">{{ $order['buyer_initials'] }}</div>
              <div>
                <div class="uname">{{ $order['buyer_name'] }}</div>
                <div class="uslug">{{ $order['buyer_email'] }}</div>
              </div>
            </div>
          </td>
          <td>
            <div class="ucell">
              <div class="uav" style="background:linear-gradient(135deg,#10b981,#059669);">
                {{ $order['seller_initials'] }}
              </div>
              <div>
                <div class="uname">{{ $order['seller_name'] }}</div>
                <div class="uslug">{{ $order['seller_username'] }}</div>
              </div>
            </div>
          </td>
          <td><div class="price-val">Rp {{ number_format($order['total'], 0, ',', '.') }}</div></td>
          <td>
            @if($order['status'] === 'paid')        <span class="badge b-ok">Berhasil</span>
            @elseif($order['status'] === 'pending')  <span class="badge b-warn">Pending</span>
            @elseif($order['status'] === 'dispute')  <span class="badge b-dispute">Dispute</span>
            @elseif($order['status'] === 'failed')   <span class="badge b-off">Gagal</span>
            @elseif($order['status'] === 'refunded') <span class="badge b-off">Refunded</span>
            @else <span class="badge" style="color:var(--ink3);background:#f7f9ff;">{{ ucfirst($order['status']) }}</span>
            @endif
          </td>
          <td>
            <div class="action-wrap">
              <a href="{{ route('admin.orders.show', $order['id']) }}" class="act-btn">Detail</a>
              @if($order['status'] === 'dispute')
                <a href="{{ route('admin.orders.show', $order['id']) }}" class="act-btn dispute">Handle</a>
              @endif

            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;padding:40px;color:var(--ink4);font-weight:600;">Tidak ada order ditemukan</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="pag-wrap">
      <div class="pag-info">
        @if($orders->total() > 0)
          Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ number_format($orders->total()) }} order
        @else
          Tidak ada order
        @endif
      </div>
      <div class="pag-btns">
        @if($orders->onFirstPage())
          <button class="pag-btn" disabled>←</button>
        @else
          <a href="{{ $orders->previousPageUrl() }}" class="pag-btn">←</a>
        @endif
        @foreach($orders->getUrlRange(max(1,$orders->currentPage()-2), min($orders->lastPage(),$orders->currentPage()+2)) as $pg => $url)
          <a href="{{ $url }}" class="pag-btn {{ $pg == $orders->currentPage() ? 'active' : '' }}">{{ $pg }}</a>
        @endforeach
        @if($orders->hasMorePages())
          <a href="{{ $orders->nextPageUrl() }}" class="pag-btn">→</a>
        @else
          <button class="pag-btn" disabled>→</button>
        @endif
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function applyFilter() {
  const url = new URL(window.location.href);
  url.searchParams.set('search', document.getElementById('searchInput').value);
  url.searchParams.set('type',   document.getElementById('typeFilter').value);
  url.searchParams.set('status', document.getElementById('statusFilter').value);
  url.searchParams.set('page', 1);
  window.location.href = url.toString();
}
</script>
@endpush