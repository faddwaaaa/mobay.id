@extends('layouts.dashboard')

@section('title', 'Produk | Payou.id')

@section('content')

<style>
* { box-sizing: border-box; }

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-header h1 { font-size: 22px; font-weight: 700; }
.subtitle { color: #64748b; font-size: 14px; }

.btn-primary {
    background: #2563eb; color: #fff; padding: 10px 16px; border-radius: 12px;
    font-weight: 600; border: none; cursor: pointer; text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-primary:hover { opacity: .9; }

/* ===== DROPDOWN ===== */
.add-product-wrapper { position: relative; display: inline-block; }
.add-dropdown {
    display: none; position: absolute; right: 0; top: calc(100% + 6px);
    background: #ffffff; border-radius: 14px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.14); min-width: 200px;
    overflow: hidden; z-index: 999; border: 1px solid #e5e7eb;
    animation: dropdownFadeIn 0.15s ease;
}
@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.add-dropdown a {
    display: flex; align-items: center; gap: 10px;
    padding: 13px 16px; text-decoration: none; color: #1e293b;
    font-size: 14px; font-weight: 500; transition: background 0.15s;
}
.add-dropdown a:hover { background: #f1f5f9; }
.add-dropdown a:not(:last-child) { border-bottom: 1px solid #f1f5f9; }
.add-dropdown .dropdown-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.add-dropdown .dropdown-icon.digital { background: #eff6ff; }
.add-dropdown .dropdown-icon.fisik   { background: #f0fdf4; }

/* ===== TAB NAVIGATION ===== */
.prod-tabs {
    display: flex; gap: 0; margin-bottom: 24px;
    border-bottom: 2px solid #e5e7eb;
    justify-content: space-between;
}
.prod-tab {
    flex: 1; justify-content: center;
    padding: 12px 16px; font-size: 14px; font-weight: 600; color: #6b7280;
    cursor: pointer; border: none; background: none; position: relative;
    bottom: -2px; border-bottom: 2px solid transparent;
    transition: color 0.15s, border-color 0.15s, background 0.15s;
    display: inline-flex; align-items: center; gap: 7px;
    border-radius: 8px 8px 0 0;
}
.prod-tab:hover { color: #2563eb; background: #f8fafc; }
.prod-tab.active { color: #2563eb; border-bottom-color: #2563eb; background: #eff6ff; }
.prod-tab-badge {
    font-size: 10px; font-weight: 700; padding: 1px 7px;
    border-radius: 20px; background: #eff6ff; color: #2563eb;
}
.prod-tab.active .prod-tab-badge { background: #2563eb; color: #fff; }

/* ===== PANELS ===== */
.prod-panel { display: none; }
.prod-panel.active { display: block; }

/* ===== PRODUCT CARD ===== */
.product-card {
    background: #fff; border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    transition: all 0.25s ease; overflow: hidden;
    display: flex; flex-direction: column; border: 1px solid #f1f5f9;
}
.product-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.11); }
.product-img-wrap {
    width: 100%; aspect-ratio: 1/1; background: #f8fafc;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; position: relative;
}
.product-img-wrap img { width: 100%; height: 100%; object-fit: contain; padding: 6px; }
.product-img-placeholder {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; gap: 8px; color: #cbd5e1; height: 100%; width: 100%;
}
.product-card-body { padding: 12px 14px 14px; display: flex; flex-direction: column; flex: 1; }
.product-card-title { font-weight: 600; font-size: 13px; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 3px; }
.product-card-desc  { font-size: 11px; color: #94a3b8; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-card-price { margin-bottom: 10px; flex: 1; }
.product-card-actions { display: flex; justify-content: space-between; gap: 8px; }
.btn-edit, .btn-delete {
    flex: 1; font-size: 12px; font-weight: 600; padding: 7px 0;
    border-radius: 8px; border: none; cursor: pointer; transition: all 0.15s; text-align: center;
}
.btn-edit         { background: #eff6ff; color: #2563eb; }
.btn-edit:hover   { background: #dbeafe; }
.btn-delete       { background: #fff1f2; color: #e11d48; }
.btn-delete:hover { background: #ffe4e6; }

/* ===== PAGINATION ===== */
.pagination-wrap {
    display: flex; justify-content: center; align-items: center;
    gap: 5px; margin-top: 28px;
}
.pag-btn {
    min-width: 36px; height: 36px; padding: 0 10px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 600; cursor: pointer;
    border: 1.5px solid #e5e7eb; background: #fff; color: #374151;
    transition: all 0.15s; text-decoration: none; user-select: none;
}
.pag-btn:hover:not(.disabled):not(.active) { border-color: #2563eb; color: #2563eb; background: #eff6ff; }
.pag-btn.active   { background: #2563eb; color: #fff; border-color: #2563eb; }
.pag-btn.disabled { opacity: 0.38; pointer-events: none; }
.pag-dots { color: #9ca3af; font-size: 14px; padding: 0 3px; line-height: 36px; }
.pag-info { text-align: center; font-size: 12px; color: #9ca3af; margin-top: 10px; }

/* ===== STATISTIK ===== */
.stat-summary-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px;
}
@media(max-width: 600px) { .stat-summary-grid { grid-template-columns: 1fr 1fr; } }
.stat-card { background: #fff; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 12px rgba(0,0,0,.06); border: 1px solid #f1f5f9; }
.stat-card-label { font-size: 11px; color: #6b7280; font-weight: 700; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px; }
.stat-card-value { font-size: 22px; font-weight: 700; color: #111827; }
.stat-card-sub   { font-size: 11px; color: #10b981; margin-top: 3px; }
.stat-table-wrap { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; border: 1px solid #f1f5f9; }
.stat-table-head { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
table.stat-table { width: 100%; border-collapse: collapse; }
table.stat-table th { padding: 10px 16px; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; background: #f8fafc; }
table.stat-table th:not(:first-child) { text-align: center; }
table.stat-table td { padding: 13px 16px; font-size: 13px; color: #374151; border-top: 1px solid #f1f5f9; }
table.stat-table td:not(:first-child) { text-align: center; }
table.stat-table tbody tr:hover { background: #f8fafc; }
.stat-rank { width: 22px; height: 22px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; color: #374151; }
.stat-rank.gold   { background: #fef9c3; color: #a16207; }
.stat-rank.silver { background: #f1f5f9; color: #475569; }
.stat-rank.bronze { background: #fef3e2; color: #b45309; }

/* ===== EMPTY STATE ===== */
.empty-state { grid-column: 1/-1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 18px; border: 2px dashed #e5e7eb; }
.empty-icon  { width: 80px; height: 80px; background: linear-gradient(135deg, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #fff; font-size: 32px; }
.empty-state h3 { font-size: 20px; font-weight: 600; margin-bottom: 8px; }
.empty-state p  { color: #64748b; margin-bottom: 20px; }
.content-section { background: #fff; padding: 20px; border-radius: 18px; box-shadow: 0 10px 28px rgba(0,0,0,.06); }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px 10px; font-size: 13px; }
thead { background: #f8fafc; }
tbody tr:hover { background: #f1f5f9; }
</style>

{{-- ===== ADD PRODUCT FORM ===== --}}
@if($showForm)
    @if($productTypeForm === 'digital')
        @include('products._form_digital')
    @else
        @include('products._form_fisik')
    @endif
@else

{{-- ===== HEADER ===== --}}
@if($products->count())
<div style="margin-bottom: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 8px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('dashboard') }}" style="width: 36px; height: 36px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s;">
                <i class="fas fa-arrow-left" style="font-size: 14px; color: #475569;"></i>
            </a>
            <div>
                <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #000000;">Produk Saya</h1>
                <p style="margin: 0; font-size: 14px; color: #797979;">Kelola semua produk digital kamu</p>
            </div>
        </div>

        {{-- DROPDOWN TAMBAH PRODUK --}}
        <div class="add-product-wrapper" id="headerDropdownWrapper">
            <button class="btn-primary" onclick="toggleDropdown('headerDropdown', event)">
                <i class="fas fa-plus"></i> Tambah Produk
                <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 2px;"></i>
            </button>
            <div class="add-dropdown" id="headerDropdown">
                <a href="{{ route('products.manage', ['tambah' => 'digital']) }}">
                    <div class="dropdown-icon digital">
                        <i class="fas fa-file-download" style="color: #2563eb; font-size: 13px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 13px;">Produk Digital</div>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 1px;">File, template, e-book</div>
                    </div>
                </a>
                <a href="{{ route('products.manage', ['tambah' => 'fisik']) }}">
                    <div class="dropdown-icon fisik">
                        <i class="fas fa-box" style="color: #16a34a; font-size: 13px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 13px;">Produk Fisik</div>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 1px;">Produk Fisik, barang</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ===== EMPTY STATE ===== --}}
@if(!$products->count())
<div class="empty-state">
    <div class="empty-icon"><i class="fas fa-box-open"></i></div>
    <h3>Belum ada produk</h3>
    <p>Tambahkan produk pertama kamu untuk mulai berjualan</p>
    <div class="add-product-wrapper" id="emptyDropdownWrapper" style="display: inline-block;">
        <button class="btn-primary" onclick="toggleDropdown('emptyDropdown', event)">
            <i class="fas fa-plus"></i> Tambah Produk
            <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 2px;"></i>
        </button>
        <div class="add-dropdown" id="emptyDropdown" style="left: 50%; transform: translateX(-50%); right: auto;">
            <a href="{{ route('products.manage', ['tambah' => 'digital']) }}">
                <div class="dropdown-icon digital">
                    <i class="fas fa-file-download" style="color: #2563eb; font-size: 13px;"></i>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 13px;">Produk Digital</div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 1px;">File, template, e-book</div>
                </div>
            </a>
            <a href="{{ route('products.manage', ['tambah' => 'fisik']) }}">
                <div class="dropdown-icon fisik">
                    <i class="fas fa-box" style="color: #16a34a; font-size: 13px;"></i>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 13px;">Produk Fisik</div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 1px;">Produk Fisik, barang</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endif

{{-- ===== TAB NAV + PANELS (hanya kalau ada produk) ===== --}}
@if($products->count())

<div class="prod-tabs">
    <button class="prod-tab {{ request('tab', 'produk') !== 'statistik' ? 'active' : '' }}"
            id="tab-btn-produk" onclick="switchTab('produk')">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
        </svg>
        Produk
        <span class="prod-tab-badge">{{ $products->total() }}</span>
    </button>
    <button class="prod-tab {{ request('tab') === 'statistik' ? 'active' : '' }}"
            id="tab-btn-statistik" onclick="switchTab('statistik')">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Statistik
    </button>
</div>

{{-- ══════════════════════════════
     PANEL: PRODUK
══════════════════════════════ --}}
<div class="prod-panel {{ request('tab', 'produk') !== 'statistik' ? 'active' : '' }}" id="panel-produk">

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
        @foreach($products as $product)
        <div class="product-card">

            <div class="product-img-wrap">
                @if($product->images->first())
                    <img src="{{ asset('storage/'.$product->images->first()->image) }}" alt="{{ $product->title }}">
                @else
                    <div class="product-img-placeholder">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span style="font-size: 11px;">Tidak ada foto</span>
                    </div>
                @endif
                @if($product->product_type === 'digital')
                    <span style="position:absolute; top:8px; left:8px; font-size:10px; background:#eff6ff; color:#2563eb; padding:2px 8px; border-radius:999px; font-weight:700; box-shadow:0 1px 4px rgba(0,0,0,0.08);">Digital</span>
                @else
                    <span style="position:absolute; top:8px; left:8px; font-size:10px; background:#f0fdf4; color:#16a34a; padding:2px 8px; border-radius:999px; font-weight:700; box-shadow:0 1px 4px rgba(0,0,0,0.08);">Fisik</span>
                @endif
            </div>

            <div class="product-card-body">
                <div class="product-card-title">{{ $product->title }}</div>
                <div class="product-card-desc">{{ $product->description }}</div>

                <div class="product-card-price">
                    @if($product->discount && $product->discount > 0)
                        @php $savedPercent = round((($product->price - $product->discount) / $product->price) * 100); @endphp
                        <div style="font-size:11px; color:#94a3b8; text-decoration:line-through;">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <div style="font-weight:700; color:#2563eb; font-size:14px;">Rp {{ number_format($product->discount, 0, ',', '.') }}</div>
                        <div style="font-size:10px; color:#e11d48; font-weight:600;">Hemat {{ $savedPercent }}%</div>
                    @else
                        <div style="font-weight:700; color:#2563eb; font-size:14px;">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    @endif
                </div>

                <div class="product-card-actions">
                    <button onclick="openEditModal({{ $product->id }})" class="btn-edit">
                        <i class="fas fa-pen" style="font-size:10px; margin-right:4px;"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" style="flex:1; display:flex;">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Yakin hapus produk?')" class="btn-delete" style="width:100%;">
                            <i class="fas fa-trash" style="font-size:10px; margin-right:4px;"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── PAGINATION ── --}}
    @if($products->lastPage() > 1)
    @php
        $curPage  = $products->currentPage();
        $lastPage = $products->lastPage();
        $show = [];
        for ($p = 1; $p <= $lastPage; $p++) {
            if ($p === 1 || $p === $lastPage || abs($p - $curPage) <= 1) {
                $show[] = $p;
            }
        }
        $show = array_unique($show);
        sort($show);
    @endphp
    <div class="pagination-wrap">
        {{-- Prev --}}
        @if($products->onFirstPage())
            <span class="pag-btn disabled">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a class="pag-btn" href="{{ $products->previousPageUrl() }}&tab=produk">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Nomor halaman --}}
        @php $prev = null; @endphp
        @foreach($show as $pageNum)
            @if($prev !== null && $pageNum - $prev > 1)
                <span class="pag-dots">&#8230;</span>
            @endif
            @if($pageNum === $curPage)
                <span class="pag-btn active">{{ $pageNum }}</span>
            @else
                <a class="pag-btn" href="{{ $products->url($pageNum) }}&tab=produk">{{ $pageNum }}</a>
            @endif
            @php $prev = $pageNum; @endphp
        @endforeach

        {{-- Next --}}
        @if($products->hasMorePages())
            <a class="pag-btn" href="{{ $products->nextPageUrl() }}&tab=produk">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="pag-btn disabled">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif
    </div>
    <p class="pag-info">
        Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} produk
    </p>
    @endif
</div>

{{-- ══════════════════════════════
     PANEL: STATISTIK
══════════════════════════════ --}}
<div class="prod-panel {{ request('tab') === 'statistik' ? 'active' : '' }}" id="panel-statistik">

    @php
        $totalProducts = $allProducts->count();
        $totalSold     = $allProducts->sum('sold');
        $totalViews    = $allProducts->sum('views_count');
        $digitalCount  = $allProducts->where('product_type', 'digital')->count();
        $fisikCount    = $allProducts->where('product_type', 'fisik')->count();
        $totalRevenue  = $allProducts->sum(function ($p) {
            $price = ($p->discount && $p->discount > 0) ? $p->discount : $p->price;
            return ($p->total_qty ?? 0) * $price;
        });
    @endphp

    {{-- Kartu ringkasan --}}
    <div class="stat-summary-grid">
        <div class="stat-card">
            <div class="stat-card-label">Total Produk</div>
            <div class="stat-card-value">{{ $totalProducts }}</div>
            <div style="font-size:11px; color:#6b7280; margin-top:3px;">{{ $digitalCount }} Digital &middot; {{ $fisikCount }} Fisik</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Total Terjual</div>
            <div class="stat-card-value">{{ number_format($totalSold) }}</div>
            <div class="stat-card-sub">unit</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Total Pendapatan</div>
            <div class="stat-card-value" style="font-size:17px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="stat-card-sub">dari semua produk</div>
        </div>
    </div>

    {{-- Tabel lifetime sales (sama persis dari versi asli, cuma dipindah ke sini) --}}
    <div class="stat-table-wrap">
        <div class="stat-table-head">
            <span style="font-size:15px; font-weight:700; color:#111827;">Statistik Penjualan Produk</span>
            <span style="font-size:12px; color:#9ca3af;">{{ $totalProducts }} produk</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="stat-table">
                <thead>
                    <tr>
                        <th style="width:36px; text-align:center;">#</th>
                        <th style="text-align:left;">Produk</th>
                        <th>Views</th>
                        <th>Sold</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allProducts->sortByDesc('sold')->values() as $i => $product)
                    @php
                        $effectivePrice = ($product->discount && $product->discount > 0) ? $product->discount : $product->price;
                        $revenue        = ($product->total_qty ?? 0) * $effectivePrice;
                        $rankClass      = $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : ''));
                    @endphp
                    <tr>
                        <td style="text-align:center;"><span class="stat-rank {{ $rankClass }}">{{ $i + 1 }}</span></td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                @php $firstImage = $product->images->first(); @endphp
                                <img src="{{ $firstImage ? asset('storage/'.$firstImage->image) : 'https://via.placeholder.com/40' }}"
                                     style="width:40px; height:40px; border-radius:8px; object-fit:cover; flex-shrink:0;">
                                <div>
                                    <div style="font-weight:600; font-size:13px; color:#111827;">{{ $product->title }}</div>
                                    <span style="font-size:10px; padding:1px 7px; border-radius:20px; font-weight:600;
                                        {{ $product->product_type === 'digital' ? 'background:#eff6ff; color:#2563eb;' : 'background:#f0fdf4; color:#16a34a;' }}">
                                        {{ $product->product_type === 'digital' ? 'Digital' : 'Fisik' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>{{ number_format($product->views_count ?? 0) }}</td>
                        <td>{{ number_format($product->sold ?? 0) }}</td>
                        <td style="font-weight:600; color:#2563eb;">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- /panel-statistik --}}

@endif {{-- products->count() --}}

@endif {{-- showForm --}}

@include('products.edit')

<script>
// ===== DROPDOWN =====
function toggleDropdown(id, event) {
    event.stopPropagation();
    const el   = document.getElementById(id);
    const open = el.style.display === 'block';
    document.querySelectorAll('.add-dropdown').forEach(d => d.style.display = 'none');
    if (!open) el.style.display = 'block';
}
document.addEventListener('click', function () {
    document.querySelectorAll('.add-dropdown').forEach(d => d.style.display = 'none');
});

// ===== TAB SWITCH =====
function switchTab(name) {
    document.querySelectorAll('.prod-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-btn-' + name)?.classList.add('active');
    document.querySelectorAll('.prod-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + name)?.classList.add('active');
    // Update URL tanpa reload supaya tab terjaga saat pagination diklik
    const url = new URL(window.location.href);
    url.searchParams.set('tab', name);
    if (name === 'statistik') url.searchParams.delete('page');
    history.replaceState(null, '', url.toString());
}

// ===== EDIT MODAL =====
function openEditModal(id) {
    const modal   = document.getElementById('editModal-' + id);
    const overlay = document.getElementById('editModalOverlay-' + id);
    const card    = document.getElementById('editModalCard-' + id);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.pointerEvents = 'auto';
    if (overlay) { overlay.classList.remove('hidden'); requestAnimationFrame(() => overlay.style.opacity = '1'); }
    if (card)    { requestAnimationFrame(() => { card.style.opacity = '1'; card.style.transform = 'translateY(0)'; }); }
    document.body.style.overflow = 'hidden';
}
function closeEditModal(id) {
    const modal   = document.getElementById('editModal-' + id);
    const overlay = document.getElementById('editModalOverlay-' + id);
    const card    = document.getElementById('editModalCard-' + id);
    if (overlay) overlay.style.opacity = '0';
    if (card)    { card.style.opacity = '0'; card.style.transform = 'translateY(20px)'; }
    setTimeout(() => {
        if (modal)   { modal.style.display = 'none'; modal.classList.add('hidden'); }
        if (overlay) overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}
</script>

@endsection