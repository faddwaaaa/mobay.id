@extends('admin.layouts.app')
@section('page-title', 'Toko & Produk')

@push('styles')
<style>
/* ── Tokens ── */
:root {
  --r: #dc2626; --r-bg: #fef2f2;
  --g: #16a34a; --g-bg: #f0fdf4;
  --b: #2563eb; --b-bg: #eff6ff;
  --a: #d97706; --a-bg: #fffbeb;
  --ink0: var(--ink,#0f172a);
  --ink2: #475569; --ink3: #94a3b8;
  --card-bg: #ffffff;
  --line-c: #e2e8f0;
  --hover-bg: #f8fafc;
  --transition: .18s cubic-bezier(.4,0,.2,1);
}

/* ── Grid top ── */
.top-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 22px;
}
.stat-card {
  background: var(--card-bg);
  border-radius: 14px;
  border: 1px solid var(--line-c);
  padding: 18px 20px 16px;
  display: flex; flex-direction: column; gap: 10px;
  transition: box-shadow var(--transition), border-color var(--transition), transform var(--transition);
  cursor: default;
}
.stat-card:hover {
  box-shadow: 0 8px 24px rgba(0,0,0,.07);
  border-color: #cbd5e1;
  transform: translateY(-2px);
}
.stat-icon-wrap {
  width: 38px; height: 38px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.stat-icon-wrap.blue  { background: #eff6ff; color: #2563eb; }
.stat-icon-wrap.green { background: #f0fdf4; color: #16a34a; }
.stat-icon-wrap.amber { background: #fffbeb; color: #d97706; }
.stat-icon-wrap.purple{ background: #f5f3ff; color: #7c3aed; }

.stat-val { font-size: 28px; font-weight: 800; color: var(--ink0); letter-spacing: -.8px; line-height: 1; }
.stat-lbl { font-size: 12px; color: var(--ink2); font-weight: 600; }
.stat-tag {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 700; padding: 3px 9px;
  border-radius: 20px; width: fit-content;
}
.tag-up     { color: var(--g); background: var(--g-bg); }
.tag-neutral{ color: var(--b); background: var(--b-bg); }

/* ── Filter bar ── */
.filter-bar {
  display: flex; gap: 10px; align-items: center;
  margin-bottom: 16px; flex-wrap: wrap;
}
.search-wrap {
  display: flex; align-items: center; gap: 8px;
  background: var(--card-bg);
  border: 1px solid var(--line-c);
  border-radius: 10px;
  padding: 9px 14px; flex: 1; min-width: 200px;
  transition: border-color var(--transition), box-shadow var(--transition);
}
.search-wrap:focus-within {
  border-color: #93c5fd;
  box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}
.search-wrap input {
  border: none; outline: none; background: none;
  font-size: 13px; color: var(--ink0); width: 100%;
}
.search-icon { color: var(--ink3); flex-shrink: 0; }

.fselect {
  border: 1px solid var(--line-c);
  border-radius: 10px;
  padding: 9px 14px;
  font-size: 13px; font-weight: 600;
  background: var(--card-bg); color: var(--ink0);
  cursor: pointer; outline: none;
  transition: border-color var(--transition), box-shadow var(--transition);
}
.fselect:focus {
  border-color: #93c5fd;
  box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}

/* ── Table card ── */
.table-card {
  background: var(--card-bg);
  border-radius: 16px;
  border: 1px solid var(--line-c);
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
table { width: 100%; border-collapse: collapse; }
thead { background: #f8fafc; }
th {
  font-size: 10.5px; font-weight: 800;
  color: var(--ink3); text-align: left;
  padding: 12px 18px; letter-spacing: .7px;
  text-transform: uppercase;
  border-bottom: 1px solid var(--line-c);
}
td {
  font-size: 13px; color: var(--ink0);
  padding: 14px 18px;
  border-bottom: 1px solid var(--line-c);
  vertical-align: middle;
}
tr:last-child td { border-bottom: none; }
tbody tr {
  transition: background var(--transition);
}
tbody tr:hover td { background: var(--hover-bg); }

/* ── Seller cell ── */
.ucell { display: flex; align-items: center; gap: 10px; }
.uav {
  width: 32px; height: 32px; border-radius: 8px;
  background: linear-gradient(135deg,#3b82f6,#6366f1);
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 900; color: white;
  flex-shrink: 0; letter-spacing: -.5px;
}
.uname { font-size: 13px; font-weight: 700; color: var(--ink0); }
.uslug { font-size: 11px; color: var(--ink3); font-family: var(--mono, monospace); }

/* ── Product cell ── */
.prod-name { font-size: 13px; font-weight: 700; color: var(--ink0); margin-bottom: 2px; }
.prod-meta { font-size: 11.5px; color: var(--ink3); }
.prod-discount {
  display: inline-block;
  font-size: 10.5px; font-weight: 700;
  color: var(--r); background: var(--r-bg);
  padding: 1px 6px; border-radius: 5px;
  margin-left: 4px;
}

/* ── Type badge ── */
.type-tag {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 11.5px; font-weight: 700;
  padding: 4px 10px; border-radius: 20px;
}
.type-digital  { color: var(--b); background: var(--b-bg); }
.type-physical { color: var(--g); background: var(--g-bg); }

/* ── Price ── */
.price-val { font-size: 13px; font-weight: 800; font-family: var(--mono, monospace); color: var(--ink0); }
.price-original {
  font-size: 11px; color: var(--ink3);
  text-decoration: line-through;
  font-family: var(--mono, monospace);
}

/* ── Sales ── */
.num { font-family: var(--mono, monospace); font-size: 13px; font-weight: 700; }

/* ── Action ── */
.act-btn {
  display: inline-flex; align-items: center; gap: 5px;
  border: 1px solid var(--line-c);
  border-radius: 8px; padding: 5px 12px;
  font-size: 12px; font-weight: 700;
  cursor: pointer; background: var(--card-bg);
  color: var(--ink0); text-decoration: none;
  transition: background var(--transition), border-color var(--transition),
              box-shadow var(--transition), transform var(--transition);
}
.act-btn:hover {
  background: var(--b-bg);
  border-color: #93c5fd;
  box-shadow: 0 2px 8px rgba(59,130,246,.12);
  transform: translateY(-1px);
  color: var(--b);
}
.act-btn:active { transform: translateY(0); }

/* ── Pagination ── */
.pag-wrap {
  display: flex; align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  border-top: 1px solid var(--line-c);
  background: #fafbfc;
}
.pag-info { font-size: 12px; color: var(--ink3); font-weight: 600; }
.pag-btns { display: flex; gap: 4px; }
.pag-btn {
  border: 1px solid var(--line-c); border-radius: 8px;
  padding: 5px 11px; font-size: 12px; font-weight: 700;
  cursor: pointer; background: var(--card-bg); color: var(--ink0);
  text-decoration: none; display: inline-block;
  transition: background var(--transition), border-color var(--transition), color var(--transition);
}
.pag-btn.active { background: var(--b); color: white; border-color: var(--b); }
.pag-btn:hover:not(.active) { background: var(--hover-bg); border-color: #cbd5e1; }
button.pag-btn:disabled { opacity: .35; cursor: not-allowed; }

/* ── Empty state ── */
.empty-state {
  text-align: center; padding: 60px 20px;
  color: var(--ink3); font-weight: 600; font-size: 13px;
}
</style>
@endpush

@section('content')
<div style="display:flex;flex-direction:column;gap:0;">

  {{-- ── STAT CARDS ── --}}
  <div class="top-grid">

    {{-- Total Produk --}}
    <div class="stat-card">
      <div class="stat-icon-wrap blue">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
          <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
          <line x1="12" y1="22.08" x2="12" y2="12"/>
        </svg>
      </div>
      <div>
        <div class="stat-val">{{ number_format($stats['total_products']) }}</div>
        <div class="stat-lbl">Total Produk</div>
      </div>
      <span class="stat-tag tag-up">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="18 15 12 9 6 15"/></svg>
        +{{ $stats['new_products_today'] }} hari ini
      </span>
    </div>

    {{-- Digital --}}
    <div class="stat-card">
      <div class="stat-icon-wrap purple">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
          <line x1="8" y1="21" x2="16" y2="21"/>
          <line x1="12" y1="17" x2="12" y2="21"/>
        </svg>
      </div>
      <div>
        <div class="stat-val">{{ number_format($stats['digital_products']) }}</div>
        <div class="stat-lbl">Produk Digital</div>
      </div>
      <span class="stat-tag tag-neutral">{{ $stats['digital_percent'] }}% dari total</span>
    </div>

    {{-- Fisik --}}
    <div class="stat-card">
      <div class="stat-icon-wrap green">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
      </div>
      <div>
        <div class="stat-val">{{ number_format($stats['physical_products']) }}</div>
        <div class="stat-lbl">Produk Fisik</div>
      </div>
      <span class="stat-tag tag-neutral">{{ $stats['physical_percent'] }}% dari total</span>
    </div>

    {{-- Terdaftar --}}
    <div class="stat-card">
      <div class="stat-icon-wrap amber">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
      </div>
      <div>
        <div class="stat-val">{{ number_format($stats['total_products']) }}</div>
        <div class="stat-lbl">Total Terdaftar</div>
      </div>
      <span class="stat-tag tag-neutral">Semua aktif</span>
    </div>

  </div>

  {{-- ── FILTER ── --}}
  <div class="filter-bar">
    <div class="search-wrap">
      <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" id="searchInput" placeholder="Cari nama produk atau seller…"
        value="{{ request('search') }}" onkeydown="if(event.key==='Enter')applyFilter()"/>
    </div>
    <select class="fselect" id="typeFilter" onchange="applyFilter()">
      <option value="">Semua Tipe</option>
      <option value="digital" {{ request('type') === 'digital' ? 'selected' : '' }}>Digital</option>
      <option value="fisik"   {{ request('type') === 'fisik'   ? 'selected' : '' }}>Fisik</option>
    </select>
    <button onclick="applyFilter()" class="act-btn" style="white-space:nowrap">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/>
        <line x1="11" y1="18" x2="13" y2="18"/>
      </svg>
      Filter
    </button>
  </div>

  {{-- ── TABLE ── --}}
  <div class="table-card">
    <table>
      <thead>
        <tr>
          <th>Produk</th>
          <th>Seller</th>
          <th>Tipe</th>
          <th>Harga</th>
          <th>Terjual</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
        @php
          $discountPercent = ($product->discount && $product->price > 0)
            ? round((1 - $product->discount / $product->price) * 100)
            : null;
        @endphp
        <tr>
          {{-- Produk --}}
          <td>
            <div class="prod-name">{{ $product->title }}</div>
            <div class="prod-meta">
              @if($product->product_type === 'fisik')
                Stok: {{ $product->stock ?? 0 }} &middot; {{ $product->weight ?? 0 }}g
              @else
                Unduhan digital
              @endif
              @if($discountPercent !== null)
                <span class="prod-discount">-{{ $discountPercent }}%</span>
              @endif
            </div>
          </td>

          {{-- Seller --}}
          <td>
            <div class="ucell">
              <div class="uav">{{ strtoupper(substr($product->owner->name ?? 'U', 0, 2)) }}</div>
              <div>
                <div class="uname">{{ $product->owner->name ?? '-' }}</div>
                <div class="uslug">{{ $product->owner->username ?? '-' }}</div>
              </div>
            </div>
          </td>

          {{-- Tipe --}}
          <td>
            @if($product->product_type === 'fisik')
              <span class="type-tag type-physical">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Fisik
              </span>
            @else
              <span class="type-tag type-digital">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                Digital
              </span>
            @endif
          </td>

          {{-- Harga --}}
          <td>
            @if($discountPercent !== null)
              <div class="price-original">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
              <div class="price-val">Rp {{ number_format($product->discount, 0, ',', '.') }}</div>
            @else
              <div class="price-val">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
            @endif
          </td>

          {{-- Terjual --}}
          <td>
            <span class="num">{{ number_format($product->sales_count ?? 0) }}</span>
          </td>

          {{-- Aksi --}}
          <td>
            <a href="{{ route('admin.products.show', $product->id) }}" class="act-btn">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              Detail
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 12px;opacity:.3">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
              Tidak ada produk ditemukan
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Pagination --}}
    <div class="pag-wrap">
      <div class="pag-info">
        Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }}
        dari {{ number_format($products->total()) }} produk
      </div>
      <div class="pag-btns">
        @if($products->onFirstPage())
          <button class="pag-btn" disabled>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
        @else
          <a href="{{ $products->previousPageUrl() }}" class="pag-btn">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
          </a>
        @endif

        @foreach($products->getUrlRange(max(1,$products->currentPage()-2), min($products->lastPage(),$products->currentPage()+2)) as $pg => $url)
          <a href="{{ $url }}" class="pag-btn {{ $pg == $products->currentPage() ? 'active' : '' }}">{{ $pg }}</a>
        @endforeach

        @if($products->hasMorePages())
          <a href="{{ $products->nextPageUrl() }}" class="pag-btn">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        @else
          <button class="pag-btn" disabled>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
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
  url.searchParams.set('page', 1);
  window.location.href = url.toString();
}
</script>
@endpush