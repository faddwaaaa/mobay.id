@extends('layouts.dashboard')

@section('title', 'Produk')

@section('content')

<style>
/* =========================================================
   GLOBAL
========================================================= */
* { box-sizing: border-box; }

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.page-header h1 {
    font-size: 22px;
    font-weight: 700;
}
.subtitle {
    color: #64748b;
    font-size: 14px;
}

/* =========================================================
   BUTTON
========================================================= */
.btn-primary {
    background: #2563eb;
    color: #fff;
    padding: 10px 16px;
    border-radius: 12px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-primary:hover { opacity: .9; }

/* =========================================================
   PRODUCT GRID
========================================================= */
.links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

/* =========================================================
   PRODUCT CARD (BERSIH, NO GARIS BIRU)
========================================================= */
.link-card {
    background: #fff;
    border-radius: 18px;
    padding: 18px;
    box-shadow: 0 10px 28px rgba(0,0,0,.06);
    transition: .25s ease;
    border: none;
}
.link-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 40px rgba(0,0,0,.08);
}

.link-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* =========================================================
   ICON
========================================================= */
.link-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    overflow: hidden;
}
.link-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* =========================================================
   STATUS
========================================================= */
.product-status {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 999px;
    font-weight: 600;
}
.product-status.active {
    background: #dcfce7;
    color: #166534;
}
.product-status.inactive {
    background: #fee2e2;
    color: #991b1b;
}

/* =========================================================
   TEXT
========================================================= */
.link-card h3 {
    margin: 12px 0 6px;
    font-size: 16px;
}
.link-description {
    font-size: 13px;
    color: #64748b;
    min-height: 38px;
}

/* =========================================================
   STATS
========================================================= */
.link-stats {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #475569;
    margin: 12px 0;
}

/* =========================================================
   PRICE
========================================================= */
.product-price { margin-bottom: 14px; }
.original-price {
    font-size: 12px;
    text-decoration: line-through;
    color: #94a3b8;
    margin-right: 6px;
}
.discount-price {
    color: #dc2626;
    font-weight: 700;
}
.normal-price {
    font-weight: 700;
    color: #2563eb;
}

/* =========================================================
   ACTIONS
========================================================= */
.link-actions {
    display: flex;
    gap: 8px;
}
.btn-action {
    flex: 1;
    border: none;
    border-radius: 12px;
    padding: 8px 0;
    background: #f1f5f9;
    cursor: pointer;
    transition: .2s;
}
.btn-action:hover { background: #e2e8f0; }
.btn-action.delete:hover {
    background: #fee2e2;
    color: #991b1b;
}

/* =========================================================
   EMPTY STATE
========================================================= */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 18px;
    border: 2px dashed #e5e7eb;
}
.empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #2563eb);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: #fff;
    font-size: 32px;
}
.empty-state h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
}
.empty-state p {
    color: #64748b;
    margin-bottom: 20px;
}

/* =========================================================
   TABLE
========================================================= */
.content-section {
    background: #fff;
    padding: 20px;
    border-radius: 18px;
    box-shadow: 0 10px 28px rgba(0,0,0,.06);
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px 10px;
    font-size: 13px;
}
thead { background: #f8fafc; }
tbody tr:hover { background: #f1f5f9; }

.status-badge {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
}
.status-badge.active {
    background: #dcfce7;
    color: #166534;
}
.status-badge.inactive {
    background: #fee2e2;
    color: #991b1b;
}
</style>

{{-- ================= HEADER ================= --}}
@if($products->count() && !$showForm)
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl font-bold">Produk Saya</h1>
        <p class="text-sm text-gray-500">Kelola semua produk digital kamu</p>
    </div>

    <a href="{{ route('products.manage', ['tambah' => 1]) }}"
       class="btn-primary">
        <i class="fas fa-plus"></i> Tambah Produk
    </a>
</div>
@endif



{{-- ================= EMPTY STATE ================= --}}
@if(!$products->count() && !$showForm)
<div class="empty-state">
    <div class="empty-icon">
        <i class="fas fa-box-open"></i>
    </div>
    <h3>Belum ada produk</h3>
    <p>Tambahkan produk pertama kamu untuk mulai berjualan</p>

    <a href="{{ route('products.manage', ['tambah' => 1]) }}"
       class="btn-primary">
        <i class="fas fa-plus"></i> Tambah Produk
    </a>
</div>
@endif


{{-- ================= ADD PRODUCT FORM ================= --}}
@if($showForm)


    @include('products._form')
</div>
@endif


{{-- ================= PRODUCT LIST ================= --}}
@if($products->count() && !$showForm)
<div class="links-grid">
@foreach($products as $product)
    <div class="link-card">
        <div class="link-card-header">
            <div class="link-icon">
                <i class="fas fa-box"></i>
            </div>

            <!-- <span class="product-status {{ $product->is_active ? 'active' : 'inactive' }}">
                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
            </span> -->
        </div>

        <h3>{{ $product->title }}</h3>
        <p class="link-description">
            {{ Str::limit($product->description ?? 'Tidak ada deskripsi', 70) }}
        </p>

        <div class="product-price">
            @if($product->discount)
                <span class="original-price">
                    Rp {{ number_format($product->price,0,',','.') }}
                </span>
                <span class="discount-price">
                    Rp {{ number_format($product->discount,0,',','.') }}
                </span>
            @else
                <span class="normal-price">
                    Rp {{ number_format($product->price,0,',','.') }}
                </span>
            @endif
        </div>

        <div class="link-actions">
            <button class="btn-action edit">
                <i class="fas fa-pen"></i>
            </button>

            <form method="POST" action="{{ route('products.destroy', $product) }}">
                @csrf
                @method('DELETE')
                <button class="btn-action delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
@endforeach
</div>
@endif

<script>
document.querySelectorAll('.btn-action.copy').forEach(btn => {
    btn.onclick = () => {
        navigator.clipboard.writeText(btn.dataset.link);
        alert('Link produk disalin');
    }
});
</script>

@endsection
