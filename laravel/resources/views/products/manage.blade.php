@extends('layouts.dashboard')

@section('title', 'Produk | Payou.id')

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
   PRODUCT CARD
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

{{-- ================= ADD PRODUCT FORM ================= --}}
@if($showForm)

    @include('products._form', ['product' => $product ?? null])

@else

    {{-- ================= HEADER ================= --}}
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
            <a href="{{ route('products.manage', ['tambah' => 1]) }}" class="btn-primary">
                <i class="fas fa-plus"></i> Tambah Produk
            </a>
        </div>
    </div>
    @endif


    {{-- ================= EMPTY STATE ================= --}}
    @if(!$products->count())
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


    {{-- ================= GRID PRODUK ================= --}}
    @if($products->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
        @foreach($products as $product)
        <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

            <div class="h-40 bg-gray-100">
                @if($product->images->first())
                    <img src="{{ asset('storage/'.$product->images->first()->image) }}"
                        class="w-full h-full object-cover">
                @else
                    <div class="flex items-center justify-center h-full text-gray-400 text-sm">
                        Tidak ada gambar
                    </div>
                @endif
            </div>

            <div class="p-4">
                <h3 class="font-semibold text-sm mb-1 truncate">
                    {{ $product->title }}
                </h3>

                <div class="text-xs text-gray-500 mb-2">
                    {{ Str::limit($product->description, 40) }}
                </div>

                {{-- ===== HARGA (DIPERBAIKI) ===== --}}
                {{--
                    $product->discount = harga SETELAH diskon (bukan persentase)
                    Jadi jika discount ada, tampilkan:
                      - harga normal (coret)
                      - harga diskon (merah)
                      - persentase hemat = (price - discount) / price * 100
                --}}
                <div class="mb-3">
                    @if($product->discount && $product->discount > 0)

                        @php
                            $savedAmount  = $product->price - $product->discount;
                            $savedPercent = round(($savedAmount / $product->price) * 100);
                        @endphp

                        <div class="text-xs text-gray-400 line-through">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>

                        <div class="font-bold text-red-600 text-sm">
                            Rp {{ number_format($product->discount, 0, ',', '.') }}
                        </div>

                        <div class="text-xs text-red-500">
                            Hemat {{ $savedPercent }}%
                        </div>

                    @else

                        <div class="font-bold text-blue-600 text-sm">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>

                    @endif
                </div>

                <div class="flex justify-between items-center">
                    <button onclick="openEditModal({{ $product->id }})"
                            class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-lg hover:bg-blue-200 transition">
                        Edit
                    </button>

                    <form method="POST" action="{{ route('products.destroy', $product) }}">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Yakin hapus produk?')"
                            class="text-xs bg-red-100 text-red-600 px-3 py-1 rounded-lg hover:bg-red-200 transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif


    {{-- ================= TABLE REALTIME ================= --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold mb-4">Product Lifetime Sales</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-left p-3">Produk</th>
                        <th class="text-center p-3">Views</th>
                        <th class="text-center p-3">Sold</th>
                        <th class="text-center p-3">Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($products as $product)
                    <tr class="border-t">
                        <td class="p-3 flex items-center gap-3">
                            @php $firstImage = $product->images->first(); @endphp
                            <img src="{{ $firstImage
                                ? asset('storage/'.$firstImage->image)
                                : 'https://via.placeholder.com/50' }}"
                                class="w-10 h-10 rounded object-cover">
                            {{ $product->title }}
                        </td>

                        <td class="text-center p-3">
                            {{ $product->views_count ?? 0 }}
                        </td>

                        <td class="text-center p-3">
                            {{ $product->sold ?? 0 }}
                        </td>

                        <td class="text-center p-3">
                            @php
                                $effectivePrice = ($product->discount && $product->discount > 0)
                                    ? $product->discount
                                    : $product->price;
                            @endphp
                            Rp {{ number_format(($product->total_qty ?? 0) * $effectivePrice, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endif {{-- INI PENUTUP showForm --}}

@include('products.edit')

<script>
function openEditModal(id){
    const modal = document.getElementById('editModal-'+id);
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeEditModal(id){
    const modal = document.getElementById('editModal-'+id);
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}
</script>

@endsection