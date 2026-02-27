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

/* ===== DROPDOWN STYLES ===== */
.add-product-wrapper {
    position: relative;
    display: inline-block;
}

.add-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 6px);
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.14);
    min-width: 200px;
    overflow: hidden;
    z-index: 999;
    border: 1px solid #e5e7eb;
    animation: dropdownFadeIn 0.15s ease;
}

@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.add-dropdown a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 13px 16px;
    text-decoration: none;
    color: #1e293b;
    font-size: 14px;
    font-weight: 500;
    transition: background 0.15s;
}

.add-dropdown a:hover { background: #f1f5f9; }

.add-dropdown a:not(:last-child) {
    border-bottom: 1px solid #f1f5f9;
}

.add-dropdown .dropdown-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.add-dropdown .dropdown-icon.digital { background: #eff6ff; }
.add-dropdown .dropdown-icon.fisik   { background: #f0fdf4; }

.links-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; margin-bottom: 40px; }

.link-card { background: #fff; border-radius: 18px; padding: 18px; box-shadow: 0 10px 28px rgba(0,0,0,.06); transition: .25s ease; border: none; }
.link-card:hover { transform: translateY(-4px); box-shadow: 0 18px 40px rgba(0,0,0,.08); }

.empty-state { grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 18px; border: 2px dashed #e5e7eb; }
.empty-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #fff; font-size: 32px; }
.empty-state h3 { font-size: 20px; font-weight: 600; margin-bottom: 8px; }
.empty-state p { color: #64748b; margin-bottom: 20px; }

.content-section { background: #fff; padding: 20px; border-radius: 18px; box-shadow: 0 10px 28px rgba(0,0,0,.06); }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px 10px; font-size: 13px; }
thead { background: #f8fafc; }
tbody tr:hover { background: #f1f5f9; }
</style>

{{-- ================= ADD PRODUCT FORM ================= --}}
@if($showForm)
    @if($productTypeForm === 'digital')
        @include('products._form_digital')
    @else
        @include('products._form_fisik')
    @endif
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

            {{-- DROPDOWN TAMBAH PRODUK - HEADER --}}
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

    {{-- ================= EMPTY STATE ================= --}}
    @if(!$products->count())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-box-open"></i></div>
        <h3>Belum ada produk</h3>
        <p>Tambahkan produk pertama kamu untuk mulai berjualan</p>

        {{-- DROPDOWN TAMBAH PRODUK - EMPTY STATE --}}
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

    {{-- ================= GRID PRODUK ================= --}}
    @if($products->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
        @foreach($products as $product)
        <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
            <div class="h-40 bg-gray-100">
                @if($product->images->first())
                    <img src="{{ asset('storage/'.$product->images->first()->image) }}" class="w-full h-full object-cover">
                @else
                    <div class="flex items-center justify-center h-full text-gray-400 text-sm">Tidak ada gambar</div>
                @endif
            </div>
            <div class="p-4">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-semibold text-sm truncate flex-1">{{ $product->title }}</h3>
                    {{-- Badge tipe produk --}}
                    @if($product->product_type === 'digital')
                        <span style="font-size: 10px; background: #eff6ff; color: #2563eb; padding: 2px 7px; border-radius: 999px; font-weight: 600; white-space: nowrap;">Digital</span>
                    @else
                        <span style="font-size: 10px; background: #f0fdf4; color: #16a34a; padding: 2px 7px; border-radius: 999px; font-weight: 600; white-space: nowrap;">Fisik</span>
                    @endif
                </div>
                <div class="text-xs text-gray-500 mb-2">{{ Str::limit($product->description, 40) }}</div>
                <div class="mb-3">
                    @if($product->discount && $product->discount > 0)
                        @php $savedPercent = round((($product->price - $product->discount) / $product->price) * 100); @endphp
                        <div class="text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <div class="font-bold text-blue-600 text-sm">Rp {{ number_format($product->discount, 0, ',', '.') }}</div>
                        <div class="text-xs text-red-500">Hemat {{ $savedPercent }}%</div>
                    @else
                        <div class="font-bold text-blue-600 text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
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

    {{-- ================= TABLE LIFETIME SALES ================= --}}
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
                            <img src="{{ $firstImage ? asset('storage/'.$firstImage->image) : 'https://via.placeholder.com/50' }}"
                                 class="w-10 h-10 rounded object-cover">
                            {{ $product->title }}
                        </td>
                        <td class="text-center p-3">{{ $product->views_count ?? 0 }}</td>
                        <td class="text-center p-3">{{ $product->sold ?? 0 }}</td>
                        <td class="text-center p-3">
                            @php $effectivePrice = ($product->discount && $product->discount > 0) ? $product->discount : $product->price; @endphp
                            Rp {{ number_format(($product->total_qty ?? 0) * $effectivePrice, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endif {{-- penutup showForm --}}

@include('products.edit')

<script>
// ===== DROPDOWN TOGGLE =====
function toggleDropdown(id, event) {
    event.stopPropagation();
    const dropdown = document.getElementById(id);
    const isOpen = dropdown.style.display === 'block';

    // Tutup semua dropdown dulu
    document.querySelectorAll('.add-dropdown').forEach(d => d.style.display = 'none');

    if (!isOpen) {
        dropdown.style.display = 'block';
    }
}

// Klik di luar untuk tutup dropdown
document.addEventListener('click', function() {
    document.querySelectorAll('.add-dropdown').forEach(d => d.style.display = 'none');
});

// ===== EDIT MODAL =====
function openEditModal(id) {
    const modal = document.getElementById('editModal-' + id);
    if (!modal) return;
    const overlay = document.getElementById('editModalOverlay-' + id);
    const card    = document.getElementById('editModalCard-' + id);
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