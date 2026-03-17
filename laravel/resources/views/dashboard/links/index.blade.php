@extends('layouts.dashboard')
@section('title', 'Link Saya | Payou.id')

@section('content')
@php
    $user = auth()->user();
@endphp

<div class="max-w-7xl mx-auto px-4 py-6" id="main-content">
    {{-- Header --}}
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            <a href="{{ route('dashboard') }}" style="width: 36px; height: 36px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s;">
                <i class="fas fa-arrow-left" style="font-size: 14px; color: #475569;"></i>
            </a>
            <div>
                <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #000000;">
                    Link Saya
                </h1>

                <div style="display: flex; align-items: center; gap: 5px; margin-top: 4px;">
                    <span style="font-size: 14px; color: #797979;">Link Saya :</span>

                    <div class="px-3 py-1 bg-blue-50 border border-blue-200 rounded-lg">
                        <a href="{{ url('/' . $user->username) }}"
                        target="_blank"
                        class="text-blue-700 font-medium text-sm">
                            {{ url('/' . $user->username) }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DUA KOLOM - UKURAN NORMAL -->
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- KIRI - CONTENT (UKURAN NORMAL) -->
        <div class="w-full lg:w-2/3 flex-shrink-0 space-y-8">

            <!-- YOUR PAGES -->
            <div class="bg-white rounded-xl shadow border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Halaman Saya</h3>
                        <button type="button" onclick="showAddPageForm()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Halaman</span>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    @if($pages->count() > 0)
                    <div class="space-y-4">
                        @foreach($pages as $page)
                        <div class="page-item group rounded-lg border p-4 transition cursor-pointer {{ $activePage && $activePage->id == $page->id ? 'bg-blue-50 border-blue-300' : 'bg-gray-50 border-gray-200 hover:border-blue-300 hover:bg-blue-50' }}"
                             data-page-id="{{ $page->id }}"
                             onclick="selectPage({{ $page->id }})">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-link text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $page->title }}</p>
                                        <p class="text-sm text-gray-500">/{{ $page->slug }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                                    @if(strtolower($page->title) !== 'utama')
                                    <button type="button" 
                                            onclick="showEditModal({{ $page->id }}, '{{ $page->title }}')"
                                            class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <form action="{{ route('pages.destroy', $page) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" 
                                                class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                        Halaman Utama
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-folder-open text-3xl mb-3"></i>
                        <p class="font-medium">Belum ada halaman</p>
                        <p class="text-sm mt-1">Klik "Tambah Halaman" untuk membuat halaman baru</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- BLOCK LIST -->
            <div class="bg-white rounded-xl shadow border border-gray-200 relative overflow-hidden" id="blockSectionCard">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="font-bold text-gray-900 text-lg" id="blockSectionHeading">
                            @if($activePage)
                                Konten: <span class="text-blue-600">{{ $activePage->title }}</span>
                            @else
                                Pilih Halaman
                            @endif
                        </h2>
                        @if($activePage)
                        <span class="text-sm text-gray-500">Seret untuk mengatur urutan</span>
                        @endif
                    </div>
                </div>

                <!-- ADD BLOCK -->
                @if($activePage)
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-medium text-gray-900 mb-4">Tambahkan blok baru</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                        <button type="button" onclick="addBlock('text')" 
                                class="border border-gray-300 rounded-xl p-4 hover:bg-blue-50 hover:border-blue-300 transition text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-font text-blue-600 text-xl mb-2"></i>
                                <span class="text-sm font-medium">Teks</span>
                            </div>
                        </button>
                        <button type="button" onclick="addBlock('image')" 
                                class="border border-gray-300 rounded-xl p-4 hover:bg-green-50 hover:border-green-300 transition text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-image text-green-600 text-xl mb-2"></i>
                                <span class="text-sm font-medium">Gambar</span>
                            </div>
                        </button>
                        <button type="button" onclick="addBlock('link')" 
                                class="border border-gray-300 rounded-xl p-4 hover:bg-purple-50 hover:border-purple-300 transition text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-link text-purple-600 text-xl mb-2"></i>
                                <span class="text-sm font-medium">Link</span>
                            </div>
                        </button>
                        <button type="button" onclick="addBlock('video')" 
                                class="border border-gray-300 rounded-xl p-4 hover:bg-red-50 hover:border-red-300 transition text-center">
                            <div class="flex flex-col items-center">
                                <i class="fab fa-youtube text-red-600 text-xl mb-2"></i>
                                <span class="text-sm font-medium">Video</span>
                            </div>
                        </button>
                        <button type="button" onclick="openProductModal()" 
                                class="border border-gray-300 rounded-xl p-4 hover:bg-yellow-50 hover:border-yellow-300 transition text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-box text-yellow-600 text-xl mb-2"></i>
                                <span class="text-sm font-medium">Produk</span>
                            </div>
                        </button>
                    </div>
                </div>
                @endif

                <!-- BLOCK DATA -->
                <div class="p-6" id="blockSectionContent">
                    @if($activePage)
                        @if($activePage->blocks->count() > 0)
                            <div id="blockList" class="space-y-3">
                                @foreach($activePage->blocks->sortBy('position') as $block)
                                <div data-id="{{ $block->id }}" 
                                     class="block-item bg-white rounded-lg border border-gray-200 p-4 hover:border-blue-300 hover:shadow-sm transition cursor-move">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-start gap-3 flex-1 min-w-0">
                                            <div class="mt-1">
                                                <i class="fas fa-grip-vertical text-gray-400"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-2">
                                                    @switch($block->type)
                                                        @case('text')
                                                            <i class="fas fa-font text-blue-600"></i>
                                                            <span class="font-medium text-gray-900">Teks</span>
                                                            @break
                                                        @case('image')
                                                            <i class="fas fa-image text-green-600"></i>
                                                            <span class="font-medium text-gray-900">Gambar</span>
                                                            @break
                                                        @case('link')
                                                            <i class="fas fa-link text-purple-600"></i>
                                                            <span class="font-medium text-gray-900">Link</span>
                                                            @break
                                                        @case('video')
                                                            <i class="fab fa-youtube text-red-600"></i>
                                                            <span class="font-medium text-gray-900">Video</span>
                                                            @break
                                                        @case('product')
                                                            <i class="fas fa-box text-yellow-600"></i>
                                                            <span class="font-medium text-gray-900">Produk</span>
                                                            @if(isset($block->product_id) && $block->product_id)
                                                            <span class="text-xs bg-yellow-50 text-yellow-600 border border-yellow-200 px-2 py-0.5 rounded-full">
                                                                ID #{{ $block->product_id }}
                                                            </span>
                                                            @endif
                                                            @break
                                                    @endswitch
                                                </div>
                                                
                                                @if($block->type === 'text' && isset($block->content['text']))
                                                    <p class="text-sm text-gray-600 line-clamp-2">{{ $block->content['text'] }}</p>
                                                @elseif($block->type === 'link' && isset($block->content['title']))
                                                    <p class="text-sm text-gray-600 font-medium">{{ $block->content['title'] }}</p>
                                                    <p class="text-xs text-gray-400 truncate">{{ $block->content['url'] ?? '' }}</p>
                                                @elseif($block->type === 'image' && isset($block->content['image']))
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ asset('storage/' . $block->content['image']) }}" 
                                                             class="w-16 h-16 object-cover rounded-lg" alt="Block image">
                                                    </div>
                                                @elseif($block->type === 'video' && isset($block->content['youtube_url']))
                                                    <div class="flex items-center gap-2">
                                                        <i class="fab fa-youtube text-red-500"></i>
                                                        <p class="text-xs text-gray-600 truncate">{{ $block->content['youtube_url'] }}</p>
                                                    </div>
                                                    @if(isset($block->content['youtube_id']))
                                                        <p class="text-xs text-gray-400 mt-1">Video ID: {{ $block->content['youtube_id'] }}</p>
                                                    @endif
                                                @elseif($block->type === 'product')
                                                    @php
                                                        $blockProduct = $block->product;
                                                        $blockPrice = (int) ($blockProduct->price ?? 0);
                                                        $blockDiscount = (int) ($blockProduct->discount ?? 0);
                                                        $blockFinalPrice = ($blockDiscount > 0 && $blockDiscount < $blockPrice) ? $blockDiscount : $blockPrice;
                                                    @endphp
                                                    <div>
                                                        @if($blockProduct)
                                                            <div class="flex items-center gap-2">
                                                                @if($blockProduct->image)
                                                                    <img src="{{ asset('storage/' . $blockProduct->image) }}"
                                                                         class="w-12 h-12 object-cover rounded-lg flex-shrink-0"
                                                                         alt="{{ $blockProduct->title }}">
                                                                @else
                                                                    <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                                                        <i class="fas fa-box text-yellow-500 text-sm"></i>
                                                                    </div>
                                                                @endif
                                                                <div class="min-w-0">
                                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $blockProduct->title }}</p>
                                                                    <div class="flex items-center gap-1 mt-1">
                                                                        <span class="text-xs font-semibold text-blue-600">Rp {{ number_format($blockFinalPrice, 0, ',', '.') }}</span>
                                                                        @if($blockFinalPrice < $blockPrice)
                                                                            <span class="text-[10px] text-gray-400 line-through">Rp {{ number_format($blockPrice, 0, ',', '.') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <p class="text-xs text-gray-400">Produk tidak ditemukan</p>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-1">
                                            @if($block->type === 'product')
                                                <button type="button"
                                                        onclick="openReplaceProductModal({{ $block->id }})"
                                                        class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition"
                                                        title="Ganti produk">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </button>
                                            @else
                                                <button type="button" 
                                                        onclick="editBlock({{ $block->id }}, '{{ $block->type }}', {{ json_encode($block->content) }})"
                                                        class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </button>
                                            @endif
                                            <button type="button" 
                                                    onclick="deleteBlock({{ $block->id }})"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400 border-2 border-dashed border-gray-300 rounded-lg">
                                <i class="fas fa-cubes text-3xl mb-3"></i>
                                <p class="font-medium">Belum ada blok</p>
                                <p class="text-sm mt-1">Tambahkan blok untuk menampilkan konten</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-mouse-pointer text-3xl mb-3"></i>
                            <p class="font-medium">Pilih halaman untuk mengelola konten</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- KANAN - PREVIEW -->
        <div class="w-full lg:w-1/3 flex-shrink-0">
            <div id="preview-sticky-wrapper">
                <div class="mb-4">
                    <h3 class="font-bold text-gray-900 mb-2">Preview</h3>
                    <p class="text-sm text-gray-600">Tampilan di mobile device</p>
                </div>
                
                <div style="position:relative;width:290px;height:550px;border-radius:42px;background:linear-gradient(160deg,#dde0e4 0%,#c2c7cc 40%,#d4d8db 70%,#b0b5ba 100%);box-shadow:0 0 0 1px rgba(255,255,255,0.6),0 0 0 2.5px #909599,0 0 0 3.5px #636870,0 0 0 5px #bec3c8,0 20px 44px rgba(0,0,0,0.35),inset 0 1px 0 rgba(255,255,255,0.45);">
                    <div style="position:absolute;right:-3px;top:130px;width:3px;height:56px;background:linear-gradient(to right,#888d94,#b2b7bc);border-radius:0 3px 3px 0;"></div>
                    <div style="position:absolute;left:-3px;top:80px;width:3px;height:20px;background:linear-gradient(to left,#888d94,#b2b7bc);border-radius:3px 0 0 3px;"></div>
                    <div style="position:absolute;left:-3px;top:107px;width:3px;height:36px;background:linear-gradient(to left,#888d94,#b2b7bc);border-radius:3px 0 0 3px;"></div>
                    <div style="position:absolute;left:-3px;top:152px;width:3px;height:36px;background:linear-gradient(to left,#888d94,#b2b7bc);border-radius:3px 0 0 3px;"></div>
                    <div style="position:absolute;inset:5px;border-radius:38px;background:#080808;overflow:hidden;">
                        <div style="position:absolute;inset:0;border-radius:38px;background:#fff;overflow:hidden;">
                            <!-- Status bar -->
                            <div style="position:absolute;top:0;left:0;right:0;height:44px;z-index:30;display:flex;align-items:flex-end;justify-content:space-between;padding:0 18px 6px;background:#fff;pointer-events:none;">
                                <span style="font-size:11px;font-weight:600;color:#111;">9:41</span>
                                <div style="display:flex;align-items:center;gap:3px;">
                                    <svg width="13" height="10" viewBox="0 0 17 12" fill="#111"><rect x="0" y="7" width="3" height="5" rx="0.8"/><rect x="4.5" y="4.5" width="3" height="7.5" rx="0.8"/><rect x="9" y="2" width="3" height="10" rx="0.8"/><rect x="13.5" y="0" width="3" height="12" rx="0.8" opacity="0.3"/></svg>
                                    <svg width="12" height="10" viewBox="0 0 16 12" fill="#111"><circle cx="8" cy="10.5" r="1.5"/><path d="M3.5 6.5a6.5 6.5 0 019 0" stroke="#111" stroke-width="1.5" stroke-linecap="round" fill="none"/><path d="M1 4a10 10 0 0114 0" stroke="#111" stroke-width="1.5" stroke-linecap="round" fill="none" opacity="0.45"/></svg>
                                    <svg width="19" height="10" viewBox="0 0 25 12" fill="#111"><rect x="0.5" y="0.5" width="21" height="11" rx="2.5" stroke="#111" stroke-width="1" fill="none"/><rect x="22" y="3.5" width="2.5" height="5" rx="1" fill="#111" opacity="0.4"/><rect x="2" y="2" width="16" height="8" rx="1.5"/></svg>
                                </div>
                            </div>
                            <!-- Dynamic island / notch -->
                            <div style="position:absolute;top:10px;left:50%;transform:translateX(-50%);width:90px;height:25px;background:#080808;border-radius:14px;z-index:40;"></div>
                            <!-- iframe area -->
                            <div style="position:absolute;top:44px;left:0;right:0;bottom:0;border-radius:0 0 38px 38px;overflow:hidden;">
                                <iframe
                                    id="preview"
                                    data-src="{{ url('/preview/'.$user->username) }}?page={{ $activePage->id ?? '' }}&t={{ time() }}"
                                    frameborder="0"
                                    loading="lazy"
                                    style="position:absolute;top:0;left:0;width:375px;height:calc(100% / 0.7467);transform:scale(0.7467);transform-origin:top left;border:none;background:#fff;display:block;">
                                </iframe>
                            </div>
                            <!-- Home indicator -->
                            <div style="position:absolute;bottom:7px;left:50%;transform:translateX(-50%);width:90px;height:3px;background:rgba(0,0,0,0.22);border-radius:2px;z-index:30;pointer-events:none;"></div>
                            <!-- Glass reflection -->
                            <div style="position:absolute;inset:0;border-radius:38px;background:linear-gradient(130deg,rgba(255,255,255,0.16) 0%,rgba(255,255,255,0.05) 28%,transparent 55%);pointer-events:none;z-index:50;"></div>
                        </div>
                    </div>
                </div>      
            </div>
        </div>
    </div>
</div>

@push('modals')
{{-- ============================================================
     MODAL SYSTEM — dirender langsung di <body> via @stack('modals')
     sehingga overlay bisa menutupi sidebar (z-index: 200) sepenuhnya
============================================================ --}}

{{-- OVERLAY GLOBAL — z-index 9990 > sidebar z-index 200
     Karena ini direct child <body>, tidak ada stacking context yang menghalangi --}}
<div id="globalModalOverlay"
     class="fixed inset-0 hidden"
     style="z-index:9990; background:rgba(15,23,42,0.5); backdrop-filter:blur(5px); -webkit-backdrop-filter:blur(5px);"
     onclick="closeAllModals()">
</div>

<div id="confirmModal"
     class="modal fixed inset-0 z-[9999] hidden items-center justify-center p-4"
     style="pointer-events:none;">
    <div class="modal-container bg-white rounded-xl shadow-2xl w-full max-w-sm relative" style="pointer-events:auto;">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900" id="confirmModalTitle">Konfirmasi</h3>
            <button type="button" onclick="closeAllModals()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 leading-6" id="confirmModalMessage">Apakah Anda yakin?</p>
        </div>
        <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-xl flex justify-end gap-3">
            <button type="button" onclick="closeAllModals()" class="px-4 py-2 rounded-lg btn-secondary">Batal</button>
            <button type="button" id="confirmModalAction" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">Ya, lanjutkan</button>
        </div>
    </div>
</div>

<!-- MODAL EDIT PAGE -->
<div id="editPageModal"
     class="modal fixed inset-0 z-[9999] hidden items-center justify-center p-4"
     style="pointer-events:none;">
    <div class="modal-container bg-white rounded-xl shadow-2xl w-full max-w-md relative" style="pointer-events:auto;">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Edit Halaman</h3>
            <button type="button" onclick="closeAllModals()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editPageForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="p-6">
                <input type="hidden" name="page_id" id="edit_page_id">
                <div class="mb-4">
                    <label for="edit_page_title" class="block text-sm font-medium text-gray-700 mb-2">Judul Halaman</label>
                    <input type="text" id="edit_page_title" name="title"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan nama page" required>
                    <p class="text-xs text-gray-500 mt-2">Nama ini akan ditampilkan di dashboard dan URL</p>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeAllModals()" class="px-4 py-2 rounded-lg btn-secondary">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD PAGE -->
<div id="addPageModal"
     class="modal fixed inset-0 z-[9999] hidden items-center justify-center p-4"
     style="pointer-events:none;">
    <div class="modal-container bg-white rounded-xl shadow-2xl w-full max-w-md relative" style="pointer-events:auto;">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Tambah Halaman Baru</h3>
            <button type="button" onclick="closeAllModals()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="addPageForm" action="{{ route('pages.store') }}" method="POST">
            @csrf
            <div class="p-6">
                <div class="mb-4">
                    <label for="new_page_title" class="block text-sm font-medium text-gray-700 mb-2">Judul Halaman</label>
                    <input type="text" id="new_page_title" name="title"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan nama halaman baru" required>
                    <p class="text-xs text-gray-500 mt-2">Nama ini akan ditampilkan di dashboard dan URL</p>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeAllModals()" class="px-4 py-2 rounded-lg btn-secondary">Batal</button>
                    <button type="submit" id="addPageSubmitBtn" class="px-4 py-2 rounded-lg btn-primary">
                        <span id="addPageSubmitText">Buat Halaman</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD/EDIT BLOCK -->
<div id="blockModal"
     class="modal fixed inset-0 z-[9999] hidden items-center justify-center p-4"
     style="pointer-events:none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative" style="pointer-events:auto;">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 id="blockModalTitle" class="font-bold text-lg">Tambah Blok</h3>
            <button type="button" onclick="closeAllModals()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="blockForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="blockType">
            <input type="hidden" id="blockId">
            <input type="hidden" id="isEdit" value="0">
            <input type="hidden" id="selectedProductId" name="product_id">
            <input type="hidden" id="currentPageId" value="{{ $activePage->id ?? '' }}">

            <div id="textField" class="hidden">
                <label class="block text-sm font-medium mb-1">Teks</label>
                <textarea id="textContent" class="w-full border rounded-lg p-3" rows="4" placeholder="Masukkan teks Anda..."></textarea>
            </div>
            <div id="linkField" class="hidden">
                <label class="block text-sm font-medium mb-1">Judul</label>
                <input id="linkTitle" class="w-full border rounded-lg p-2 mb-3" placeholder="Nama link">
                <label class="block text-sm font-medium mb-1">URL</label>
                <input id="linkUrl" class="w-full border rounded-lg p-2" placeholder="https://example.com">
            </div>
            <div id="videoField" class="hidden">
                <label class="block text-sm font-medium mb-1">URL Video</label>
                <input id="youtubeUrl" class="w-full border rounded-lg p-2" placeholder="https://youtube.com/watch?v=...">
                <p class="text-xs text-gray-500 mt-1">Masukkan link video yang valid</p>
                <div id="youtubePreview" class="mt-3 hidden">
                    <p class="text-xs font-medium mb-1">Preview:</p>
                    <div id="youtubeThumbnail" class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fab fa-youtube text-red-500 text-3xl"></i>
                    </div>
                </div>
            </div>
            <div id="imageField" class="hidden">
                <label class="block text-sm font-medium mb-1">Upload Gambar</label>
                <input type="file" id="imageFile" accept="image/*" class="w-full border rounded-lg p-2">
                <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG</p>
                <div id="currentImage" class="mt-2 hidden">
                    <p class="text-xs font-medium mb-1">Preview:</p>
                    <img id="currentImagePreview" src="" class="w-20 h-20 object-cover rounded-lg">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeAllModals()" class="px-4 py-2 rounded-lg btn-secondary">Batal</button>
                <button type="submit" id="blockSubmitBtn" class="px-4 py-2 rounded-lg btn-primary">
                    <span id="submitBtnText">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PRODUK -->
<div id="productModal"
     class="modal fixed inset-0 z-[9999] hidden items-center justify-center p-4"
     style="pointer-events:none;">
    <div class="bg-white rounded-xl w-[450px] relative shadow-2xl overflow-hidden" style="pointer-events:auto;">
        <div class="p-6 pb-0">
            <button type="button" onclick="closeProductModal()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-times"></i>
            </button>
            <h2 id="productModalTitle" class="text-lg font-bold mb-4">Pilih Produk</h2>

            <div class="flex gap-1 border-b border-gray-200">
                <button type="button" id="prodTabAll" onclick="switchProdTab('all')"
                        class="prod-tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg transition-all duration-150
                               text-blue-600 border-b-2 border-blue-600 bg-blue-50 mb-[-1px]">
                    Semua
                </button>
                <button type="button" id="prodTabFisik" onclick="switchProdTab('fisik')"
                        class="prod-tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg transition-all duration-150
                               text-gray-500 border-b-2 border-transparent mb-[-1px] hover:text-gray-700 hover:bg-gray-50 flex items-center gap-1.5">
                    <i class="fas fa-box text-xs"></i> Fisik
                </button>
                <button type="button" id="prodTabDigital" onclick="switchProdTab('digital')"
                        class="prod-tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg transition-all duration-150
                               text-gray-500 border-b-2 border-transparent mb-[-1px] hover:text-gray-700 hover:bg-gray-50 flex items-center gap-1.5">
                    <i class="fas fa-download text-xs"></i> Digital
                </button>
            </div>
        </div>

        <div class="p-6 pt-4">
            @if($products->count() > 0)
                <div class="space-y-3 max-h-[360px] overflow-y-auto pr-1" id="productListContainer">
                    @foreach($products as $product)
                    <div class="product-item flex justify-between items-center p-3 border rounded-lg hover:bg-gray-50 transition"
                         data-type="{{ $product->product_type ?? 'fisik' }}">
                        <div class="flex items-center gap-3">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-12 h-12 object-cover rounded-lg flex-shrink-0" alt="{{ $product->title }}">
                            @else
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0
                                            {{ ($product->product_type ?? 'fisik') === 'digital' ? 'bg-blue-50' : 'bg-yellow-100' }}">
                                    <i class="fas {{ ($product->product_type ?? 'fisik') === 'digital' ? 'fa-download text-blue-500' : 'fa-box text-yellow-600' }}"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="font-semibold text-sm">{{ $product->title }}</h4>
                                <p class="text-xs text-gray-600">Rp {{ number_format($product->price,0,',','.') }}</p>
                                <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-medium
                                             {{ ($product->product_type ?? 'fisik') === 'digital' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600' }}">
                                    {{ ($product->product_type ?? 'fisik') === 'digital' ? 'Digital' : 'Fisik' }}
                                </span>
                            </div>
                        </div>
                        <button type="button"
                            data-product-select-btn="true"
                            data-product-id="{{ $product->id }}"
                            onclick="selectProduct({{ $product->id }}, {{ json_encode($product) }})"
                            class="flex-shrink-0 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition active:scale-95">
                            Pilih
                        </button>
                    </div>
                    @endforeach
                    <div id="prodEmptyState" class="hidden text-center py-10">
                        <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-box-open text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-500" id="prodEmptyText">Tidak ada produk</p>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                    <p class="mb-3 text-gray-500">Belum ada produk</p>
                    <a href="{{ route('products.manage', ['tambah' => 1, 'redirect' => 'builder']) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm inline-block">
                       + Tambah Produk
                    </a>
                </div>
            @endif
        </div>

        @if($products->count() > 0)
        <div class="px-6 py-4 border-t border-gray-100">
            <a href="{{ route('products.manage', ['tambah' => 1, 'redirect' => 'builder']) }}" 
               class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                <i class="fas fa-plus-circle"></i>
                Tambah Produk Baru
            </a>
        </div>
        @endif
    </div>
</div>

<style>
/* ============================================================
   MODAL STYLES — Tidak ada filter/blur pada elemen halaman.
   Blur hanya via overlay backdrop-filter.
============================================================ */

/* Default: modal tersembunyi */
.modal { display: none; }

/* Saat aktif: tampil sebagai flex */
.modal.is-open {
    display: flex !important;
}

/* Animasi masuk untuk modal container */
.modal.is-open > div {
    animation: modalSlideIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-12px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* Utility */
.page-item { transition: all 0.2s; }

.btn-primary {
    background-color: #2563eb;
    color: white;
    transition: all 0.2s ease;
    border: none;
}
.btn-primary:hover {
    background-color: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(37,99,235,0.28);
}

.btn-secondary {
    background-color: white;
    border: 1px solid #d1d5db;
    color: #374151;
    transition: all 0.2s ease;
}
.btn-secondary:hover {
    background-color: #f9fafb;
}

.sortable-ghost { opacity: 0.4; background: #dbeafe; }
.sortable-drag  { opacity: 0.8; transform: rotate(2deg); }

.block-section-busy {
    position: absolute;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.78);
    backdrop-filter: blur(3px);
    z-index: 15;
}

.block-section-busy.show {
    display: flex;
}

.block-section-busy-card {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.92);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.18);
}

.block-section-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,.28);
    border-top-color: #fff;
    border-radius: 999px;
    animation: blockSectionSpin .7s linear infinite;
}

.block-item.is-removing {
    opacity: 0.38;
    transform: scale(0.985);
    pointer-events: none;
}

@keyframes blockSectionSpin {
    to { transform: rotate(360deg); }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.preview-screen-wrap { overflow: hidden; position: relative; border-radius: 28px; }
.preview-screen-wrap iframe {
    display: block; width: calc(100% + 17px); height: 490px;
    border: none; background: white;
}

/* Sticky preview */
@media (min-width: 1024px) {
    #preview-sticky-wrapper {
        position: sticky;
        top: 0;
        padding-top: 8px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
// ============================================
// GLOBAL VARIABLES
// ============================================
let currentPageId     = {{ $activePage->id ?? 'null' }};
let _replacingBlockId = null;
let isBlockSubmitting = false;
let isAddPageSubmitting = false;
let isProductSubmitting = false;
let confirmActionHandler = null;
let blockSectionBusyCount = 0;

function setBlockSubmitState(isLoading) {
    const submitBtn = document.getElementById('blockSubmitBtn');
    const submitTxt = document.getElementById('submitBtnText');
    const isEdit = document.getElementById('isEdit')?.value === '1';

    isBlockSubmitting = isLoading;

    if (submitBtn) {
        submitBtn.disabled = isLoading;
        submitBtn.style.opacity = isLoading ? '0.7' : '';
        submitBtn.style.cursor = isLoading ? 'not-allowed' : '';
    }

    if (submitTxt) {
        submitTxt.textContent = isLoading
            ? (isEdit ? 'Menyimpan...' : 'Menambahkan...')
            : (isEdit ? 'Update' : 'Simpan');
    }
}

function setAddPageSubmitState(isLoading) {
    const submitBtn = document.getElementById('addPageSubmitBtn');
    const submitTxt = document.getElementById('addPageSubmitText');

    isAddPageSubmitting = isLoading;

    if (submitBtn) {
        submitBtn.disabled = isLoading;
        submitBtn.style.opacity = isLoading ? '0.7' : '';
        submitBtn.style.cursor = isLoading ? 'not-allowed' : '';
    }

    if (submitTxt) {
        submitTxt.textContent = isLoading ? 'Membuat...' : 'Buat Halaman';
    }
}

function setProductSubmitState(isLoading, activeProductId = null) {
    const buttons = document.querySelectorAll('[data-product-select-btn="true"]');
    isProductSubmitting = isLoading;

    buttons.forEach((btn) => {
        const isActive = activeProductId !== null && btn.getAttribute('data-product-id') === String(activeProductId);
        btn.disabled = isLoading;
        btn.style.opacity = isLoading ? '0.7' : '';
        btn.style.cursor = isLoading ? 'not-allowed' : '';
        btn.textContent = isLoading && isActive ? 'Memproses...' : 'Pilih';
    });
}

function ensureBlockSectionBusyOverlay() {
    const card = document.getElementById('blockSectionCard');
    if (!card) return null;

    let overlay = document.getElementById('blockSectionBusyOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'blockSectionBusyOverlay';
        overlay.className = 'block-section-busy';
        overlay.innerHTML = `
            <div class="block-section-busy-card">
                <span class="block-section-spinner"></span>
                <span id="blockSectionBusyText">Memproses perubahan...</span>
            </div>
        `;
        card.appendChild(overlay);
    }

    return overlay;
}

function setBlockSectionBusy(isBusy, message = 'Memproses perubahan...') {
    const overlay = ensureBlockSectionBusyOverlay();
    if (!overlay) return;

    if (isBusy) {
        blockSectionBusyCount += 1;
        const textEl = document.getElementById('blockSectionBusyText');
        if (textEl) textEl.textContent = message;
        overlay.classList.add('show');
        return;
    }

    blockSectionBusyCount = Math.max(0, blockSectionBusyCount - 1);
    if (blockSectionBusyCount === 0) {
        overlay.classList.remove('show');
    }
}

function markBlockPendingState(blockId, isPending) {
    const blockEl = document.querySelector(`#blockList [data-id="${blockId}"]`);
    if (!blockEl) return;
    blockEl.classList.toggle('is-removing', Boolean(isPending));
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function fmtMoney(value) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(Number(value || 0)));
}

function refreshPreview() {
    const iframe = document.getElementById('preview');
    if (!iframe) return;
    const baseSrc = iframe.src || iframe.dataset.src || '';
    if (!baseSrc) return;
    const cleanSrc = baseSrc.replace(/([?&])t=\d+/, '$1').replace(/[?&]$/, '');
    iframe.src = cleanSrc + (cleanSrc.includes('?') ? '&' : '?') + 't=' + Date.now();
}

function showConfirmDialog(message, onConfirm, options = {}) {
    const titleEl = document.getElementById('confirmModalTitle');
    const msgEl = document.getElementById('confirmModalMessage');
    const actionBtn = document.getElementById('confirmModalAction');
    if (titleEl) titleEl.textContent = options.title || 'Konfirmasi';
    if (msgEl) msgEl.textContent = message;
    if (actionBtn) {
        actionBtn.textContent = options.confirmText || 'Ya, lanjutkan';
        actionBtn.onclick = () => {
            closeAllModals();
            if (typeof confirmActionHandler === 'function') {
                const handler = confirmActionHandler;
                confirmActionHandler = null;
                handler();
            }
        };
    }
    confirmActionHandler = onConfirm;
    showModal('confirmModal');
}

function renderBlockContent(block) {
    const content = block.content || {};
    if (block.type === 'text' && content.text) {
        return `<p class="text-sm text-gray-600 line-clamp-2">${escapeHtml(content.text)}</p>`;
    }
    if (block.type === 'link' && content.title) {
        return `<p class="text-sm text-gray-600 font-medium">${escapeHtml(content.title)}</p>
                <p class="text-xs text-gray-400 truncate">${escapeHtml(content.url || '')}</p>`;
    }
    if (block.type === 'image' && content.image) {
        return `<div class="flex items-center gap-3">
                    <img src="/storage/${escapeHtml(content.image)}" class="w-16 h-16 object-cover rounded-lg" alt="Block image">
                </div>`;
    }
    if (block.type === 'video' && content.youtube_url) {
        const videoIdHtml = content.youtube_id
            ? `<p class="text-xs text-gray-400 mt-1">Video ID: ${escapeHtml(content.youtube_id)}</p>`
            : '';
        return `<div class="flex items-center gap-2">
                    <i class="fab fa-youtube text-red-500"></i>
                    <p class="text-xs text-gray-600 truncate">${escapeHtml(content.youtube_url)}</p>
                </div>${videoIdHtml}`;
    }
    if (block.type === 'product') {
        const product = content.product || {};
        const price = Number(product.price || 0);
        const discount = Number(product.discount || 0);
        const finalPrice = discount > 0 && discount < price ? discount : price;
        const imageHtml = product.image
            ? `<img src="/storage/${escapeHtml(product.image)}" class="w-12 h-12 object-cover rounded-lg flex-shrink-0" alt="${escapeHtml(product.title || 'Produk')}">`
            : `<div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-box text-yellow-500 text-sm"></i></div>`;
        if (!product.title) {
            return `<p class="text-xs text-gray-400">Produk tidak ditemukan</p>`;
        }
        return `<div class="flex items-center gap-2">
                    ${imageHtml}
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">${escapeHtml(product.title)}</p>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-xs font-semibold text-blue-600">${fmtMoney(finalPrice)}</span>
                            ${finalPrice < price ? `<span class="text-[10px] text-gray-400 line-through">${fmtMoney(price)}</span>` : ''}
                        </div>
                    </div>
                </div>`;
    }
    return `<p class="text-xs text-gray-400">Konten tidak tersedia</p>`;
}

function renderBlockActions(block) {
    if (block.type === 'product') {
        return `<button type="button"
                    onclick="openReplaceProductModal(${block.id})"
                    class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition"
                    title="Ganti produk">
                    <i class="fas fa-edit text-sm"></i>
                </button>
                <button type="button"
                    onclick="deleteBlock(${block.id})"
                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                    <i class="fas fa-trash text-sm"></i>
                </button>`;
    }
    const encodedContent = encodeURIComponent(JSON.stringify(block.content || {}));
    return `<button type="button"
                onclick="editBlockFromEncoded(${block.id}, '${escapeHtml(block.type)}', '${encodedContent}')"
                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                <i class="fas fa-edit text-sm"></i>
            </button>
            <button type="button"
                onclick="deleteBlock(${block.id})"
                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                <i class="fas fa-trash text-sm"></i>
            </button>`;
}

function renderBlockCard(block) {
    const iconMap = {
        text: ['fas fa-font text-blue-600', 'Teks'],
        image: ['fas fa-image text-green-600', 'Gambar'],
        link: ['fas fa-link text-purple-600', 'Link'],
        video: ['fab fa-youtube text-red-600', 'Video'],
        product: ['fas fa-box text-yellow-600', 'Produk'],
    };
    const [iconClass, label] = iconMap[block.type] || ['fas fa-cube text-gray-500', block.type];
    const badge = block.type === 'product' && block.product_id
        ? `<span class="text-xs bg-yellow-50 text-yellow-600 border border-yellow-200 px-2 py-0.5 rounded-full">ID #${escapeHtml(block.product_id)}</span>`
        : '';

    return `<div data-id="${block.id}" class="block-item bg-white rounded-lg border border-gray-200 p-4 hover:border-blue-300 hover:shadow-sm transition cursor-move">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <div class="mt-1">
                            <i class="fas fa-grip-vertical text-gray-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="${iconClass}"></i>
                                <span class="font-medium text-gray-900">${label}</span>
                                ${badge}
                            </div>
                            ${renderBlockContent(block)}
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        ${renderBlockActions(block)}
                    </div>
                </div>
            </div>`;
}

function initBlockSortable() {
    const blockList = document.getElementById('blockList');
    if (!blockList) return;
    if (blockList._sortableInstance) {
        blockList._sortableInstance.destroy();
    }
    blockList._sortableInstance = new Sortable(blockList, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        handle: '.fa-grip-vertical',
        onEnd: function() {
            const order = Array.from(blockList.children).map((item, index) => ({
                id: item.dataset.id, position: index + 1
            }));
            fetch('{{ route("blocks.reorder") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ order })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) refreshPreview();
            });
        }
    });
}

async function refreshActivePageBlocks() {
    if (!currentPageId) return;
    const res = await fetch(`/blocks?page_id=${currentPageId}`, {
        headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (!res.ok || !data.success) {
        throw new Error(data.message || 'Gagal memuat ulang blok');
    }

    const heading = document.getElementById('blockSectionHeading');
    if (heading) {
        heading.innerHTML = `Konten: <span class="text-blue-600">${escapeHtml(data.pageTitle || '')}</span>`;
    }

    const container = document.getElementById('blockSectionContent');
    if (!container) return;

    if (Array.isArray(data.blocks) && data.blocks.length > 0) {
        container.innerHTML = `<div id="blockList" class="space-y-3">${data.blocks.map(renderBlockCard).join('')}</div>`;
        initBlockSortable();
    } else {
        container.innerHTML = `<div class="text-center py-8 text-gray-400 border-2 border-dashed border-gray-300 rounded-lg">
            <i class="fas fa-cubes text-3xl mb-3"></i>
            <p class="font-medium">Belum ada blok</p>
            <p class="text-sm mt-1">Tambahkan blok untuk menampilkan konten</p>
        </div>`;
    }
}

async function syncBuilderAfterChange() {
    try {
        await refreshActivePageBlocks();
        refreshPreview();
        return true;
    } catch (error) {
        console.error('Gagal sinkronisasi builder, fallback ke reload penuh:', error);
        refreshPreview();
        window.location.reload();
        return false;
    } finally {
        blockSectionBusyCount = 0;
        const overlay = document.getElementById('blockSectionBusyOverlay');
        if (overlay) overlay.classList.remove('show');
    }
}

function editBlockFromEncoded(blockId, type, encodedContent) {
    try {
        const content = JSON.parse(decodeURIComponent(encodedContent || ''));
        editBlock(blockId, type, content);
    } catch (error) {
        showBuilderToast('Gagal membuka data blok untuk diedit', 'error');
    }
}

// ============================================
// MODAL MANAGEMENT
// ============================================
function showModal(modalId) {
    closeAllModals();

    const overlay = document.getElementById('globalModalOverlay');
    const modal   = document.getElementById(modalId);

    overlay.classList.remove('hidden');
    modal.classList.add('is-open');

    // Kunci scroll halaman, TANPA filter/blur
    document.body.style.overflow = 'hidden';
}

function closeAllModals() {
    document.querySelectorAll('.modal.is-open').forEach(m => m.classList.remove('is-open'));
    document.getElementById('globalModalOverlay').classList.add('hidden');
    document.body.style.overflow = '';
    setBlockSubmitState(false);
}

// ============================================
// PRODUCT TAB FILTER
// ============================================
function switchProdTab(tab) {
    const tabs = { all: 'prodTabAll', fisik: 'prodTabFisik', digital: 'prodTabDigital' };
    Object.entries(tabs).forEach(([key, id]) => {
        const el = document.getElementById(id);
        if (!el) return;
        if (key === tab) {
            el.classList.add('text-blue-600','border-blue-600','bg-blue-50');
            el.classList.remove('text-gray-500','border-transparent');
        } else {
            el.classList.remove('text-blue-600','border-blue-600','bg-blue-50');
            el.classList.add('text-gray-500','border-transparent');
        }
    });

    const items = document.querySelectorAll('#productListContainer .product-item');
    let visibleCount = 0;
    items.forEach(item => {
        const type = item.getAttribute('data-type') || 'fisik';
        const show = tab === 'all' || type === tab;
        item.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    const emptyState = document.getElementById('prodEmptyState');
    const emptyText  = document.getElementById('prodEmptyText');
    if (emptyState) {
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
            if (emptyText) emptyText.textContent =
                tab === 'fisik' ? 'Tidak ada produk fisik' :
                tab === 'digital' ? 'Tidak ada produk digital' : 'Tidak ada produk';
        } else {
            emptyState.classList.add('hidden');
        }
    }
}

// ============================================
// TOAST
// ============================================
function showBuilderToast(msg, type) {
    let toast = document.getElementById('builderToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'builderToast';
        toast.style.cssText = `
            position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(80px);
            padding:10px 20px;border-radius:50px;font-size:13px;font-weight:500;
            z-index:99999;opacity:0;transition:all 0.35s cubic-bezier(.34,1.56,.64,1);
            white-space:nowrap;pointer-events:none;color:white;
        `;
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.style.background = type === 'success' ? '#16a34a' : '#dc2626';
    toast.style.opacity    = '1';
    toast.style.transform  = 'translateX(-50%) translateY(0)';
    clearTimeout(toast._t);
    toast._t = setTimeout(() => {
        toast.style.opacity   = '0';
        toast.style.transform = 'translateX(-50%) translateY(80px)';
    }, 3000);
}

function hidePreviewFrameScrollbar(frameId) {
    const frame = document.getElementById(frameId);
    if (!frame) return;

    try {
        const doc = frame.contentDocument || frame.contentWindow?.document;
        if (!doc?.head || !doc.body) return;

        let styleEl = doc.getElementById('payou-hide-preview-scrollbar');
        if (!styleEl) {
            styleEl = doc.createElement('style');
            styleEl.id = 'payou-hide-preview-scrollbar';
            styleEl.textContent = `
                html, body {
                    scrollbar-width: none !important;
                    -ms-overflow-style: none !important;
                }
                html::-webkit-scrollbar,
                body::-webkit-scrollbar,
                *::-webkit-scrollbar {
                    width: 0 !important;
                    height: 0 !important;
                    display: none !important;
                    background: transparent !important;
                }
            `;
            doc.head.appendChild(styleEl);
        }
    } catch (error) {
        console.warn('Tidak bisa menyembunyikan scrollbar preview:', error);
    }
}

// ============================================
// PAGE FUNCTIONS
// ============================================
function selectPage(pageId) {
    // ── PERBAIKAN: update iframe preview terlebih dahulu, lalu redirect ──
    const iframe = document.getElementById('preview');
    if (iframe) {
        iframe.src = `{{ url('/preview/'.$user->username) }}?page=${pageId}&t=` + Date.now();
    }
    // Redirect halaman agar block list di kiri juga ikut berganti
    window.location.href = `{{ route('links.index') }}?page=${pageId}`;
}

function showAddPageForm() {
    setAddPageSubmitState(false);
    showModal('addPageModal');
    setTimeout(() => document.getElementById('new_page_title').focus(), 100);
}

function showEditModal(pageId, pageTitle) {
    document.getElementById('edit_page_id').value    = pageId;
    document.getElementById('edit_page_title').value = pageTitle;
    document.getElementById('editPageForm').action   = `/pages/${pageId}`;
    showModal('editPageModal');
    setTimeout(() => document.getElementById('edit_page_title').focus(), 100);
}

function confirmDelete(button) {
    showConfirmDialog('Yakin ingin menghapus halaman ini?', () => {
        button.closest('form').submit();
    }, {
        title: 'Hapus Halaman',
        confirmText: 'Ya, hapus'
    });
}

// ============================================
// BLOCK FUNCTIONS
// ============================================
function addBlock(type) {
    if (!currentPageId) { showBuilderToast('Silakan pilih halaman terlebih dahulu', 'error'); return; }
    document.getElementById('blockModalTitle').textContent = 'Tambah Blok ' + getBlockTypeName(type);
    document.getElementById('blockType').value = type;
    document.getElementById('isEdit').value    = '0';
    document.getElementById('blockId').value   = '';
    document.getElementById('submitBtnText').textContent = 'Simpan';

    ['textField','linkField','videoField','imageField','currentImage'].forEach(id =>
        document.getElementById(id).classList.add('hidden'));
    ['textContent','linkTitle','linkUrl','youtubeUrl','imageFile'].forEach(id =>
        { const el = document.getElementById(id); if (el) el.value = ''; });
    document.getElementById('youtubePreview').classList.add('hidden');

    // Reset compression state saat buka modal baru
    const imageInput = document.getElementById('imageFile');
    if (imageInput) imageInput._compressedFile = null;
    const compressionInfo = document.getElementById('compressionInfo');
    if (compressionInfo) compressionInfo.remove();
    setBlockSubmitState(false);

    if (type === 'text')  document.getElementById('textField').classList.remove('hidden');
    if (type === 'link')  document.getElementById('linkField').classList.remove('hidden');
    if (type === 'video') document.getElementById('videoField').classList.remove('hidden');
    if (type === 'image') document.getElementById('imageField').classList.remove('hidden');

    showModal('blockModal');
}

function editBlock(blockId, type, content) {
    document.getElementById('blockModalTitle').textContent = 'Edit Blok ' + getBlockTypeName(type);
    document.getElementById('blockType').value = type;
    document.getElementById('isEdit').value    = '1';
    document.getElementById('blockId').value   = blockId;
    document.getElementById('submitBtnText').textContent = 'Update';

    ['textField','linkField','videoField','imageField','currentImage'].forEach(id =>
        document.getElementById(id).classList.add('hidden'));

    // Reset compression state
    const imageInput = document.getElementById('imageFile');
    if (imageInput) imageInput._compressedFile = null;
    const compressionInfo = document.getElementById('compressionInfo');
    if (compressionInfo) compressionInfo.remove();
    setBlockSubmitState(false);

    if (type === 'text') {
        document.getElementById('textField').classList.remove('hidden');
        document.getElementById('textContent').value = content.text || '';
    }
    if (type === 'link') {
        document.getElementById('linkField').classList.remove('hidden');
        document.getElementById('linkTitle').value = content.title || '';
        document.getElementById('linkUrl').value   = content.url   || '';
    }
    if (type === 'video') {
        document.getElementById('videoField').classList.remove('hidden');
        document.getElementById('youtubeUrl').value = content.youtube_url || '';
        if (content.youtube_id) {
            document.getElementById('youtubePreview').classList.remove('hidden');
            document.getElementById('youtubeThumbnail').innerHTML =
                `<img src="https://img.youtube.com/vi/${content.youtube_id}/mqdefault.jpg" class="w-full h-32 object-cover rounded-lg">`;
        }
    }
    if (type === 'image') {
        document.getElementById('imageField').classList.remove('hidden');
        if (content.image) {
            document.getElementById('currentImage').classList.remove('hidden');
            document.getElementById('currentImagePreview').src = '/storage/' + content.image;
        }
    }
    showModal('blockModal');
}

function getBlockTypeName(type) {
    return { text:'Teks', image:'Gambar', link:'Link', video:'Video', product:'Produk' }[type] || type;
}

function deleteBlock(blockId) {
    showConfirmDialog('Yakin ingin menghapus blok ini?', () => {
        markBlockPendingState(blockId, true);
        setBlockSectionBusy(true, 'Menghapus blok...');
        fetch(`/blocks/${blockId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(async data => {
            if (data.success) {
                await syncBuilderAfterChange();
                showBuilderToast('Blok berhasil dihapus', 'success');
            } else {
                markBlockPendingState(blockId, false);
                setBlockSectionBusy(false);
                showBuilderToast(data.message || 'Gagal menghapus blok', 'error');
            }
        })
        .catch(() => {
            markBlockPendingState(blockId, false);
            setBlockSectionBusy(false);
            showBuilderToast('Terjadi kesalahan saat menghapus blok', 'error');
        });
    }, {
        title: 'Hapus Blok',
        confirmText: 'Ya, hapus'
    });
}

// ============================================
// YOUTUBE
// ============================================
function extractYoutubeId(url) {
    if (!url) return null;
    const patterns = [
        /youtube\.com\/watch\?v=([^&]+)/,
        /youtu\.be\/([^?]+)/,
        /youtube\.com\/embed\/([^?]+)/,
        /youtube\.com\/shorts\/([^?]+)/
    ];
    for (let p of patterns) { const m = url.match(p); if (m) return m[1]; }
    return null;
}

// ============================================
// IMAGE COMPRESSION
// ============================================
function compressImage(file, maxSizeKB = 300, maxWidth = 1280, quality = 0.82) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onerror = () => reject(new Error('Gagal membaca file'));
        reader.onload = function(e) {
            const img = new Image();
            img.onerror = () => reject(new Error('Gagal memuat gambar'));
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let { width, height } = img;

                // Scale down jika terlalu lebar
                if (width > maxWidth) {
                    height = Math.round(height * maxWidth / width);
                    width  = maxWidth;
                }

                canvas.width  = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Kompresi iteratif sampai ukuran target tercapai
                let q = quality;
                function tryCompress() {
                    canvas.toBlob(blob => {
                        if (!blob) { reject(new Error('Kompresi gagal')); return; }
                        const sizeKB = blob.size / 1024;
                        if (sizeKB > maxSizeKB && q > 0.2) {
                            q -= 0.08;
                            tryCompress();
                        } else {
                            const compressedFile = new File(
                                [blob],
                                file.name.replace(/\.[^.]+$/, '.jpg'),
                                { type: 'image/jpeg', lastModified: Date.now() }
                            );
                            resolve({
                                file:         compressedFile,
                                originalKB:   (file.size / 1024).toFixed(0),
                                compressedKB: sizeKB.toFixed(0),
                                savedKB:      ((file.size - blob.size) / 1024).toFixed(0)
                            });
                        }
                    }, 'image/jpeg', q);
                }
                tryCompress();
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function showCompressionInfo(originalKB, compressedKB, savedKB) {
    const old = document.getElementById('compressionInfo');
    if (old) old.remove();
    const el = document.createElement('div');
    el.id = 'compressionInfo';
    el.style.cssText = 'margin-top:6px;display:flex;align-items:center;gap:5px;';
    el.innerHTML = `<span style="width:7px;height:7px;border-radius:50%;background:#16a34a;display:inline-block;flex-shrink:0;"></span><span style="font-size:11px;color:#9ca3af;">Gambar siap diupload</span>`;
    document.getElementById('imageFile').parentNode.appendChild(el);
}

// ============================================
// PRODUCT FUNCTIONS
// ============================================
function openProductModal() {
    if (!currentPageId) { showBuilderToast('Silakan pilih halaman terlebih dahulu', 'error'); return; }
    _replacingBlockId = null;
    setProductSubmitState(false);
    document.getElementById('productModalTitle').textContent = 'Pilih Produk';
    switchProdTab('all');
    showModal('productModal');
}

function openReplaceProductModal(blockId) {
    _replacingBlockId = blockId;
    setProductSubmitState(false);
    document.getElementById('productModalTitle').textContent = 'Ganti Produk';
    switchProdTab('all');
    showModal('productModal');
}

function closeProductModal() {
    _replacingBlockId = null;
    setProductSubmitState(false);
    closeAllModals();
}

function selectProduct(productId, productData) {
    if (!currentPageId) { showBuilderToast('Silakan pilih halaman terlebih dahulu', 'error'); closeProductModal(); return; }
    if (isProductSubmitting) return;
    setProductSubmitState(true, productId);
    setBlockSectionBusy(true, _replacingBlockId ? 'Mengganti produk...' : 'Menambahkan produk...');

    if (_replacingBlockId) {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('product_id', productId);
        formData.append('type', 'product');
        formData.append('page_id', currentPageId);

        const blockId = _replacingBlockId;
        fetch(`/blocks/${blockId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(r => r.json())
        .then(async data => {
            if (data.success) {
                closeProductModal();
                await syncBuilderAfterChange();
                showBuilderToast('✓ Produk berhasil diganti!', 'success');
            } else {
                setProductSubmitState(false);
                setBlockSectionBusy(false);
                showBuilderToast(data.message || 'Gagal mengganti produk', 'error');
            }
        })
        .catch(() => {
            setProductSubmitState(false);
            setBlockSectionBusy(false);
            showBuilderToast('Terjadi kesalahan saat mengganti produk', 'error');
        });
    } else {
        const formData = new FormData();
        formData.append('page_id', currentPageId);
        formData.append('type', 'product');
        formData.append('product_id', productId);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("blocks.store") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(r => r.json())
        .then(async data => {
            if (data.success) {
                closeProductModal();
                await syncBuilderAfterChange();
                showBuilderToast('Produk berhasil ditambahkan', 'success');
            }
            else {
                setProductSubmitState(false);
                setBlockSectionBusy(false);
                showBuilderToast(data.message || 'Gagal menambahkan produk', 'error');
            }
        })
        .catch(() => {
            setProductSubmitState(false);
            setBlockSectionBusy(false);
            showBuilderToast('Terjadi kesalahan saat menambahkan produk', 'error');
        });
    }
}

// ============================================
// FORM SUBMISSIONS
// ============================================
document.getElementById('editPageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(this.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else showBuilderToast(data.message || 'Gagal mengupdate halaman', 'error');
    })
    .catch(() => showBuilderToast('Terjadi kesalahan saat mengupdate halaman', 'error'));
});

document.getElementById('addPageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (isAddPageSubmitting) return;

    setAddPageSubmitState(true);

    fetch(this.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this)
    })
    .then(async (r) => {
        const data = await r.json();
        return { ok: r.ok, data };
    })
    .then(({ ok, data }) => {
        if (ok && data.success) {
            location.reload();
            return;
        }
        setAddPageSubmitState(false);
        showBuilderToast(data.message || 'Gagal menambahkan halaman', 'error');
    })
    .catch(() => {
        setAddPageSubmitState(false);
        showBuilderToast('Terjadi kesalahan saat menambahkan halaman', 'error');
    });
});

document.getElementById('blockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (isBlockSubmitting) return;
    if (!currentPageId) { showBuilderToast('Silakan pilih halaman terlebih dahulu', 'error'); return; }

    const type    = document.getElementById('blockType').value;
    const isEdit  = document.getElementById('isEdit').value === '1';
    const blockId = document.getElementById('blockId').value;
    const formData = new FormData();

    formData.append('page_id', currentPageId);
    formData.append('type', type);
    formData.append('_token', '{{ csrf_token() }}');

    if (type === 'text') {
        const v = document.getElementById('textContent').value;
        if (!v && !isEdit) { showBuilderToast('Teks tidak boleh kosong', 'error'); return; }
        formData.append('content[text]', v);
    }
    if (type === 'link') {
        const t = document.getElementById('linkTitle').value;
        const u = document.getElementById('linkUrl').value;
        if (!t && !isEdit) { showBuilderToast('Judul link tidak boleh kosong', 'error'); return; }
        if (!u && !isEdit) { showBuilderToast('URL tidak boleh kosong', 'error'); return; }
        formData.append('content[title]', t);
        formData.append('content[url]', u);
    }
    if (type === 'video') {
        const u  = document.getElementById('youtubeUrl').value;
        const id = extractYoutubeId(u);
        if (!u && !isEdit) { showBuilderToast('URL YouTube tidak boleh kosong', 'error'); return; }
        if (u && !id)      { showBuilderToast('URL YouTube tidak valid', 'error'); return; }
        formData.append('content[youtube_url]', u);
        formData.append('content[youtube_id]', id);
    }
    if (type === 'image') {
        const imageInput = document.getElementById('imageFile');
        // Gunakan file terkompresi jika ada, fallback ke file asli
        const f = imageInput._compressedFile || imageInput.files[0];
        if (f)            { formData.append('image', f); }
        else if (!isEdit) { showBuilderToast('Silakan pilih gambar', 'error'); return; }
    }

    let url = '{{ route("blocks.store") }}';
    if (isEdit) { url = `/blocks/${blockId}`; formData.append('_method', 'PUT'); }

    setBlockSubmitState(true);
    setBlockSectionBusy(true, isEdit ? 'Memperbarui blok...' : 'Menambahkan blok...');

    fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: formData })
    .then(r => r.json())
    .then(async data => {
        if (data.success) {
            closeAllModals();
            await syncBuilderAfterChange();
            showBuilderToast(isEdit ? 'Blok berhasil diperbarui' : 'Blok berhasil ditambahkan', 'success');
            return;
        }
        setBlockSubmitState(false);
        setBlockSectionBusy(false);
        showBuilderToast(data.message || 'Gagal menyimpan blok', 'error');
    })
    .catch(() => {
        setBlockSubmitState(false);
        setBlockSectionBusy(false);
        showBuilderToast('Terjadi kesalahan saat menyimpan blok', 'error');
    });
});

// ============================================
// KEYBOARD & INIT
// ============================================
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAllModals(); });

document.addEventListener('DOMContentLoaded', function() {
    const previewFrame = document.getElementById('preview');
    if (previewFrame) {
        requestAnimationFrame(() => {
            if (!previewFrame.src) {
                previewFrame.src = previewFrame.dataset.src || '';
            }
        });
        previewFrame.addEventListener('load', () => hidePreviewFrameScrollbar('preview'));
    }

    initBlockSortable();

    // YouTube preview
    const youtubeInput = document.getElementById('youtubeUrl');
    if (youtubeInput) {
        youtubeInput.addEventListener('input', function() {
            const id = extractYoutubeId(this.value);
            if (id) {
                document.getElementById('youtubePreview').classList.remove('hidden');
                document.getElementById('youtubeThumbnail').innerHTML =
                    `<img src="https://img.youtube.com/vi/${id}/mqdefault.jpg" class="w-full h-32 object-cover rounded-lg">`;
            } else {
                document.getElementById('youtubePreview').classList.add('hidden');
            }
        });
    }

    // ============================================
    // AUTO-COMPRESS GAMBAR SAAT DIPILIH
    // ============================================
    const imageFileInput = document.getElementById('imageFile');
    if (imageFileInput) {
        imageFileInput._compressedFile = null;

        imageFileInput.addEventListener('change', async function() {
            const file = this.files[0];
            if (!file) return;

            // Bersihkan info kompresi lama
            const oldInfo = document.getElementById('compressionInfo');
            if (oldInfo) oldInfo.remove();

            // Tampilkan loading state — subtle
            const loadingEl = document.createElement('div');
            loadingEl.id = 'compressionInfo';
            loadingEl.style.cssText = 'margin-top:6px;display:flex;align-items:center;gap:5px;';
            loadingEl.innerHTML = `<svg style="width:11px;height:11px;animation:spin 1s linear infinite;flex-shrink:0;color:#d1d5db;" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.3"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:.7"/></svg><span style="font-size:11px;color:#9ca3af;">Memproses gambar...</span>`;
            this.parentNode.appendChild(loadingEl);

            try {
                const result = await compressImage(file);
                imageFileInput._compressedFile = result.file;
                showCompressionInfo(result.originalKB, result.compressedKB, result.savedKB);

                // Preview gambar hasil kompresi
                const reader = new FileReader();
                reader.onload = (ev) => {
                    document.getElementById('currentImage').classList.remove('hidden');
                    document.getElementById('currentImagePreview').src = ev.target.result;
                };
                reader.readAsDataURL(result.file);
            } catch (err) {
                imageFileInput._compressedFile = null;
                const errEl = document.getElementById('compressionInfo');
                if (errEl) errEl.remove();
                console.error('Kompresi gagal:', err);
            }
        });
    }

    // Auto-open product modal dari session
    var shouldOpenProductModal = {{ session('openProductModal') ? 'true' : 'false' }};
    if (shouldOpenProductModal && currentPageId) setTimeout(openProductModal, 500);

    // SCROLL LOCK saat HP preview penuh kelihatan
    (function() {
        const phoneWrap = document.getElementById('preview-sticky-wrapper');
        const rightCol  = document.querySelector('.lg\\:w-1\\/3');  // ← tambah ini
        if (!phoneWrap || window.innerWidth < 1024) return;

        let lockPhase    = false;
        let scrollBuffer = 0;
        const THRESHOLD  = 80;
        let mouseOnRight = false;  // ← tambah ini

        // Track mouse di kolom kanan
        if (rightCol) {
            rightCol.addEventListener('mouseenter', () => mouseOnRight = true);
            rightCol.addEventListener('mouseleave', () => { mouseOnRight = false; lockPhase = false; scrollBuffer = 0; });
        }

        function isPhoneFullyVisible() {
            const rect = phoneWrap.getBoundingClientRect();
            return rect.top <= 4 && rect.bottom <= window.innerHeight + 4;
        }

        window.addEventListener('scroll', function() {
            if (!isPhoneFullyVisible()) {
                lockPhase    = false;
                scrollBuffer = 0;
            } else if (!lockPhase) {
                lockPhase    = true;
                scrollBuffer = 0;
            }
        }, { passive: true });

        document.addEventListener('wheel', function(e) {
            if (!mouseOnRight) return;  // ← tambah ini: skip kalau cursor di kiri
            if (!isPhoneFullyVisible()) return;
            if (!lockPhase) return;
            scrollBuffer += Math.abs(e.deltaY);
            if (scrollBuffer < THRESHOLD) {
                e.preventDefault();
            } else {
                lockPhase    = false;
                scrollBuffer = 0;
            }
        }, { passive: false });
    })();
});
</script>
@endpush
@endsection
