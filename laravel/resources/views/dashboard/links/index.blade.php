@extends('layouts.dashboard')
@section('title', 'Link Saya | Payou.id')

@section('content')
@php
    $user = auth()->user();
    // Ambil page_id dari query parameter atau gunakan page pertama
    $selectedPageId = request()->query('page');
    $activePage = $selectedPageId 
        ? $pages->where('id', $selectedPageId)->first() 
        : $pages->first();
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
            <div class="bg-white rounded-xl shadow border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="font-bold text-gray-900 text-lg">
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

                <!-- ADD BLOCK (hanya muncul jika ada halaman yang dipilih) -->
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
                <div class="p-6">
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
                                                    <p class="text-sm text-gray-600 line-clamp-2">
                                                        {{ $block->content['text'] }}
                                                    </p>
                                                
                                                @elseif($block->type === 'link' && isset($block->content['title']))
                                                    <p class="text-sm text-gray-600 font-medium">
                                                        {{ $block->content['title'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-400 truncate">
                                                        {{ $block->content['url'] ?? '' }}
                                                    </p>
                                                
                                                @elseif($block->type === 'image' && isset($block->content['image']))
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ asset('storage/' . $block->content['image']) }}" 
                                                             class="w-16 h-16 object-cover rounded-lg"
                                                             alt="Block image">
                                                    </div>
                                                
                                                @elseif($block->type === 'video' && isset($block->content['youtube_url']))
                                                    <div class="flex items-center gap-2">
                                                        <i class="fab fa-youtube text-red-500"></i>
                                                        <p class="text-xs text-gray-600 truncate">
                                                            {{ $block->content['youtube_url'] }}
                                                        </p>
                                                    </div>
                                                    @if(isset($block->content['youtube_id']))
                                                        <p class="text-xs text-gray-400 mt-1">
                                                            Video ID: {{ $block->content['youtube_id'] }}
                                                        </p>
                                                    @endif
                                                
                                                @elseif($block->type === 'product')
                                                    <div id="block-product-info-{{ $block->id }}"
                                                         data-product-id="{{ $block->product_id ?? '' }}">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center animate-pulse">
                                                                <i class="fas fa-box text-yellow-300 text-sm"></i>
                                                            </div>
                                                            <div>
                                                                <div class="h-3 w-24 bg-gray-200 rounded animate-pulse mb-1"></div>
                                                                <div class="h-2.5 w-16 bg-gray-100 rounded animate-pulse"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-1">
                                            {{-- Tombol edit produk: buka modal ganti produk --}}
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
                
                <!-- PHONE FRAME -->
                <div class="relative mx-auto" style="width:300px; margin-left: auto; margin-right: 0;">
                    <!-- Phone Body -->
                    <div class="relative bg-gray-900 rounded-[36px] p-2 shadow-2xl">
                        <!-- Notch -->
                        <div class="mb-4"></div>
                        
                        <!-- Screen -->
                        <div class="bg-white rounded-[28px] preview-screen-wrap">
                            <iframe
                                id="preview"
                                src="{{ url('/preview/'.$user->username) }}?page={{ $activePage->id ?? '' }}&t={{ time() }}"
                                frameborder="0">
                            </iframe>
                        </div>
                        
                        <!-- Home Indicator -->
                        <div class="h-1 w-24 bg-gray-800 rounded-full mx-auto mt-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GLOBAL MODAL OVERLAY -->
<div id="globalModalOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[199] hidden transition-all duration-300"></div>

<!-- MODAL EDIT PAGE -->
<div id="editPageModal" class="modal fixed inset-0 z-[200] flex items-center justify-center p-4 hidden">
    <div class="modal-container bg-white rounded-xl shadow-lg w-full max-w-md relative">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Edit Halaman</h3>
            <button type="button" onclick="closeEditModal()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editPageForm" method="POST" action="{{ route('pages.update', ['page' => '__ID__']) }}">
            @csrf
            @method('PUT')
            <div class="p-6">
                <input type="hidden" name="page_id" id="edit_page_id">
                <div class="mb-4">
                    <label for="edit_page_title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Halaman
                    </label>
                    <input type="text" 
                           id="edit_page_title" 
                           name="title"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan nama page"
                           required>
                    <p class="text-xs text-gray-500 mt-2">
                        Nama ini akan ditampilkan di dashboard dan URL
                    </p>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 rounded-lg btn-secondary">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg btn-primary">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD PAGE -->
<div id="addPageModal" class="modal fixed inset-0 z-[200] flex items-center justify-center p-4 hidden">
    <div class="modal-container bg-white rounded-xl shadow-lg w-full max-w-md relative">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Tambah Halaman Baru</h3>
            <button type="button" onclick="closeAddModal()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="addPageForm" action="{{ route('pages.store') }}" method="POST">
            @csrf
            <div class="p-6">
                <div class="mb-4">
                    <label for="new_page_title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Halaman
                    </label>
                    <input type="text" 
                           id="new_page_title" 
                           name="title"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan nama halaman baru"
                           required>
                    <p class="text-xs text-gray-500 mt-2">
                        Nama ini akan ditampilkan di dashboard dan URL
                    </p>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeAddModal()"
                            class="px-4 py-2 rounded-lg btn-secondary">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg btn-primary">
                        Buat Halaman
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD/EDIT BLOCK -->
<div id="blockModal" class="modal fixed inset-0 z-[200] flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md relative">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 id="blockModalTitle" class="font-bold text-lg">Tambah Blok</h3>
            <button type="button" onclick="closeBlockModal()" class="text-gray-400 hover:text-gray-600">
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

            <!-- TEXT -->
            <div id="textField" class="hidden">
                <label class="block text-sm font-medium mb-1">Teks</label>
                <textarea id="textContent" class="w-full border rounded-lg p-3" rows="4" placeholder="Masukkan teks Anda..."></textarea>
            </div>

            <!-- LINK -->
            <div id="linkField" class="hidden">
                <label class="block text-sm font-medium mb-1">Judul</label>
                <input id="linkTitle" class="w-full border rounded-lg p-2 mb-3" placeholder="Nama link">
                <label class="block text-sm font-medium mb-1">URL</label>
                <input id="linkUrl" class="w-full border rounded-lg p-2" placeholder="https://example.com">
            </div>

            <!-- YOUTUBE -->
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

            <!-- IMAGE -->
            <div id="imageField" class="hidden">
                <label class="block text-sm font-medium mb-1">Upload Gambar</label>
                <input type="file" id="imageFile" accept="image/*" class="w-full border rounded-lg p-2">
                <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG (Max 5MB)</p>
                <div id="currentImage" class="mt-2 hidden">
                    <p class="text-xs font-medium mb-1">Gambar saat ini:</p>
                    <img id="currentImagePreview" src="" class="w-20 h-20 object-cover rounded-lg">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeBlockModal()" class="px-4 py-2 rounded-lg btn-secondary">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-lg btn-primary">
                    <span id="submitBtnText">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL PRODUK (dipakai untuk Tambah & Ganti Produk)
============================================================ -->
<div id="productModal" class="modal fixed inset-0 z-[200] flex items-center justify-center p-4 hidden">
    <div class="bg-white p-6 rounded-xl w-[450px] relative shadow-xl">
        <button type="button" onclick="closeProductModal()" 
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>

        <h2 id="productModalTitle" class="text-lg font-bold mb-4">Pilih Produk</h2>

        @if($products->count() > 0)
            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1">
                @foreach($products as $product)
                <div class="product-item flex justify-between items-center p-3 border rounded-lg hover:bg-gray-50">
                    <div class="flex items-center gap-3">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-12 h-12 object-cover rounded-lg" alt="{{ $product->title }}">
                        @else
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-yellow-600"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="font-semibold text-sm">{{ $product->title }}</h4>
                            <p class="text-xs text-gray-600">
                                Rp {{ number_format($product->price,0,',','.') }}
                            </p>
                        </div>
                    </div>

                    <button type="button"
                        onclick="selectProduct({{ $product->id }}, {{ json_encode($product) }})"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition">
                        Pilih
                    </button>
                </div>
                @endforeach
            </div>
            
            <div class="mt-4 pt-3 border-t">
                <a href="{{ route('products.manage', ['tambah' => 1, 'redirect' => 'builder']) }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Produk Baru
                </a>
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
</div>

<style>
.page-item { transition: all 0.2s; }

#preview-sticky-wrapper { position: relative; }

.modal { display: none; }
.modal:not(.hidden) { display: flex; }

#globalModalOverlay { pointer-events: auto; }

body.modal-open #main-content,
body.modal-open .navbar,
body.modal-open .sidebar,
body.modal-open .dashboard-sidebar,
body.modal-open header,
body.modal-open footer {
    filter: blur(3px);
    transition: filter 0.3s ease;
    pointer-events: none;
}

.modal, .modal *, #globalModalOverlay {
    filter: none !important;
    pointer-events: auto !important;
}

.modal-container { animation: modalSlideIn 0.3s ease-out; }

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.btn-primary {
    background-color: #2563eb; color: white;
    transition: all 0.2s ease; border: none;
}
.btn-primary:hover {
    background-color: #1d4ed8;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(37,99,235,0.3);
}

.btn-secondary {
    background-color: white; border: 1px solid #d1d5db;
    color: #374151; transition: all 0.2s ease;
}
.btn-secondary:hover {
    background-color: #f9fafb;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
}

.sortable-ghost { opacity: 0.4; background: #dbeafe; }
.sortable-drag  { opacity: 0.8; transform: rotate(2deg); }

.preview-screen-wrap { overflow: hidden; position: relative; border-radius: 28px; }
.preview-screen-wrap iframe {
    display: block; width: calc(100% + 17px); height: 490px;
    border: none; background: white;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
// ============================================
// LIVE FETCH PRODUCT INFO IN BLOCK LIST
// ============================================
function loadBlockProductInfos() {
    document.querySelectorAll('[id^="block-product-info-"]').forEach(container => {
        const productId = container.getAttribute('data-product-id');
        if (!productId) return;

        fetch(`/api/product/${productId}`)
            .then(r => r.json())
            .then(prod => {
                const price    = prod.price    ?? 0;
                const discount = prod.discount ?? null;
                const final    = (discount && discount > 0 && discount < price) ? discount : price;
                const hasDis   = final < price;

                const imgHtml = prod.image_url
                    ? `<img src="${prod.image_url}" class="w-12 h-12 object-cover rounded-lg" alt="">`
                    : `<div style="width:48px;height:48px;background:#fefce8;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                           <i class="fas fa-box" style="color:#ca8a04;font-size:14px;"></i>
                       </div>`;

                const priceHtml = hasDis
                    ? `<span style="font-size:11px;color:#2563eb;font-weight:600;">Rp ${fmtNum(final)}</span>
                       <span style="font-size:10px;color:#9ca3af;text-decoration:line-through;margin-left:3px;">Rp ${fmtNum(price)}</span>`
                    : `<span style="font-size:11px;color:#2563eb;font-weight:600;">Rp ${fmtNum(final)}</span>`;

                container.innerHTML = `
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="flex-shrink:0;">${imgHtml}</div>
                        <div style="min-width:0;">
                            <p style="font-size:13px;font-weight:500;color:#111827;
                                       white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
                                       max-width:180px;">${escHtml(prod.title)}</p>
                            <div style="display:flex;align-items:center;gap:4px;margin-top:2px;">
                                ${priceHtml}
                            </div>
                        </div>
                    </div>`;
            })
            .catch(() => {
                container.innerHTML = `<p style="font-size:12px;color:#9ca3af;">Produk tidak ditemukan</p>`;
            });
    });
}

function fmtNum(n) {
    return new Intl.NumberFormat('id-ID').format(Math.round(n));
}

function escHtml(str) {
    if (!str) return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ============================================
// GLOBAL VARIABLES
// ============================================
let currentPageId  = {{ $activePage->id ?? 'null' }};
let _replacingBlockId = null; // null = tambah baru, angka = ganti produk di blok ini

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
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
    clearTimeout(toast._t);
    toast._t = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(80px)';
    }, 3000);
}

// ============================================
// MODAL MANAGEMENT
// ============================================
function showModal(modalId) {
    closeAllModals();
    const overlay = document.getElementById('globalModalOverlay');
    overlay.classList.remove('hidden');
    document.body.classList.add('modal-open');
    document.getElementById(modalId).classList.remove('hidden');
}

function closeAllModals() {
    document.querySelectorAll('.modal').forEach(m => m.classList.add('hidden'));
    document.getElementById('globalModalOverlay').classList.add('hidden');
    document.body.classList.remove('modal-open');
}

// ============================================
// PAGE FUNCTIONS
// ============================================
function selectPage(pageId) {
    window.location.href = `{{ route('links.index') }}?page=${pageId}`;
}

function showAddPageForm() {
    showModal('addPageModal');
    setTimeout(() => document.getElementById('new_page_title').focus(), 100);
}

function closeAddModal() { closeAllModals(); }

function showEditModal(pageId, pageTitle) {
    const form = document.getElementById('editPageForm');
    document.getElementById('edit_page_id').value = pageId;
    document.getElementById('edit_page_title').value = pageTitle;
    form.action = `/pages/${pageId}`;
    showModal('editPageModal');
    setTimeout(() => document.getElementById('edit_page_title').focus(), 100);
}

function closeEditModal() { closeAllModals(); }

function confirmDelete(button) {
    if (confirm('Yakin ingin menghapus halaman ini?')) {
        button.closest('form').submit();
    }
}

// ============================================
// BLOCK FUNCTIONS
// ============================================
function addBlock(type) {
    if (!currentPageId) { alert('Silakan pilih halaman terlebih dahulu'); return; }
    document.getElementById('blockModalTitle').textContent = 'Tambah Blok ' + getBlockTypeName(type);
    document.getElementById('blockType').value = type;
    document.getElementById('isEdit').value = '0';
    document.getElementById('blockId').value = '';
    document.getElementById('submitBtnText').textContent = 'Simpan';

    ['textField','linkField','videoField','imageField','currentImage'].forEach(id =>
        document.getElementById(id).classList.add('hidden'));

    ['textContent','linkTitle','linkUrl','youtubeUrl','imageFile'].forEach(id =>
        document.getElementById(id).value = '');
    document.getElementById('youtubePreview').classList.add('hidden');

    if (type === 'text')  document.getElementById('textField').classList.remove('hidden');
    if (type === 'link')  document.getElementById('linkField').classList.remove('hidden');
    if (type === 'video') document.getElementById('videoField').classList.remove('hidden');
    if (type === 'image') document.getElementById('imageField').classList.remove('hidden');

    showModal('blockModal');
}

function editBlock(blockId, type, content) {
    document.getElementById('blockModalTitle').textContent = 'Edit Blok ' + getBlockTypeName(type);
    document.getElementById('blockType').value = type;
    document.getElementById('isEdit').value = '1';
    document.getElementById('blockId').value = blockId;
    document.getElementById('submitBtnText').textContent = 'Update';

    ['textField','linkField','videoField','imageField','currentImage'].forEach(id =>
        document.getElementById(id).classList.add('hidden'));

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
    if (!confirm('Yakin ingin menghapus blok ini?')) return;
    fetch(`/blocks/${blockId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); else alert('Gagal menghapus blok'); })
    .catch(() => alert('Terjadi kesalahan saat menghapus blok'));
}

function closeBlockModal() {
    document.getElementById('blockForm').reset();
    closeAllModals();
}

// ============================================
// YOUTUBE FUNCTIONS
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
// PRODUCT FUNCTIONS
// ============================================

// Buka modal untuk TAMBAH blok produk baru
function openProductModal() {
    if (!currentPageId) { alert('Silakan pilih halaman terlebih dahulu'); return; }
    _replacingBlockId = null;
    document.getElementById('productModalTitle').textContent = 'Pilih Produk';
    showModal('productModal');
}

// Buka modal untuk GANTI produk di blok yang sudah ada
function openReplaceProductModal(blockId) {
    _replacingBlockId = blockId;
    document.getElementById('productModalTitle').textContent = 'Ganti Produk';
    showModal('productModal');
}

function closeProductModal() {
    _replacingBlockId = null;
    closeAllModals();
}

function selectProduct(productId, productData) {
    if (!currentPageId) { alert('Silakan pilih halaman terlebih dahulu'); closeProductModal(); return; }

    if (_replacingBlockId) {
        // ── MODE GANTI: update product_id di blok yang sudah ada ──
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('product_id', productId);
        formData.append('type', 'product');
        formData.append('page_id', currentPageId);

        const blockId = _replacingBlockId; // simpan dulu sebelum closeProductModal reset

        fetch(`/blocks/${blockId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeProductModal();
                const iframe = document.getElementById('preview');
                if (iframe) {
                    const src = iframe.src.replace(/[&?]t=\d+/, '');
                    iframe.src = src + (src.includes('?') ? '&' : '?') + 't=' + Date.now();
                }
                showBuilderToast('✓ Produk berhasil diganti!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                alert('Gagal mengganti produk: ' + (data.message ?? ''));
            }
        })
        .catch(() => alert('Terjadi kesalahan saat mengganti produk'));

    } else {
        // ── MODE TAMBAH: buat blok produk baru ──
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
        .then(data => {
            if (data.success) { closeProductModal(); location.reload(); }
            else alert('Gagal menambahkan produk');
        })
        .catch(() => alert('Terjadi kesalahan saat menambahkan produk'));
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
    .then(data => { if (data.success) location.reload(); else alert('Gagal mengupdate halaman'); })
    .catch(() => alert('Terjadi kesalahan'));
});

document.getElementById('blockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!currentPageId) { alert('Silakan pilih halaman terlebih dahulu'); return; }

    const type   = document.getElementById('blockType').value;
    const isEdit = document.getElementById('isEdit').value === '1';
    const blockId= document.getElementById('blockId').value;
    const formData = new FormData();

    formData.append('page_id', currentPageId);
    formData.append('type', type);
    formData.append('_token', '{{ csrf_token() }}');

    if (type === 'text') {
        const v = document.getElementById('textContent').value;
        if (!v && !isEdit) { alert('Teks tidak boleh kosong'); return; }
        formData.append('content[text]', v);
    }
    if (type === 'link') {
        const t = document.getElementById('linkTitle').value;
        const u = document.getElementById('linkUrl').value;
        if (!t && !isEdit) { alert('Judul link tidak boleh kosong'); return; }
        if (!u && !isEdit) { alert('URL tidak boleh kosong'); return; }
        formData.append('content[title]', t);
        formData.append('content[url]', u);
    }
    if (type === 'video') {
        const u  = document.getElementById('youtubeUrl').value;
        const id = extractYoutubeId(u);
        if (!u && !isEdit) { alert('URL YouTube tidak boleh kosong'); return; }
        if (u && !id)      { alert('URL YouTube tidak valid'); return; }
        formData.append('content[youtube_url]', u);
        formData.append('content[youtube_id]', id);
    }
    if (type === 'image') {
        const f = document.getElementById('imageFile').files[0];
        if (f)            { formData.append('image', f); }
        else if (!isEdit) { alert('Silakan pilih gambar'); return; }
    }

    let url = '{{ route("blocks.store") }}';
    if (isEdit) { url = `/blocks/${blockId}`; formData.append('_method', 'PUT'); }

    fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: formData })
    .then(r => r.json())
    .then(data => { if (data.success) { closeBlockModal(); location.reload(); } else alert('Gagal menyimpan blok'); })
    .catch(() => alert('Terjadi kesalahan saat menyimpan blok'));
});

// ============================================
// MODAL CLOSE HANDLERS
// ============================================
document.addEventListener('click', e => { if (e.target.id === 'globalModalOverlay') closeAllModals(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAllModals(); });

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    loadBlockProductInfos();

    const blockList = document.getElementById('blockList');
    if (blockList) {
        new Sortable(blockList, {
            animation: 150, ghostClass: 'sortable-ghost', handle: '.fa-grip-vertical',
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
                .then(data => { if (data.success) document.getElementById('preview').contentWindow.location.reload(); });
            }
        });
    }

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
});

// ============================================
// STICKY PREVIEW
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    var wrapper = document.getElementById('preview-sticky-wrapper');
    if (!wrapper || window.innerWidth < 1024) return;
    var blockedByOverflow = false;
    var el = wrapper.parentElement;
    while (el && el !== document.body) {
        var s = window.getComputedStyle(el);
        if (/(auto|scroll|hidden)/.test(s.overflow + s.overflowY)) { blockedByOverflow = true; break; }
        el = el.parentElement;
    }
    if (blockedByOverflow) {
        wrapper.style.position = 'fixed'; wrapper.style.top = '24px';
        wrapper.style.right = '24px'; wrapper.style.width = '340px'; wrapper.style.zIndex = '50';
    } else {
        wrapper.style.position = 'sticky'; wrapper.style.top = '24px';
    }
});

window.addEventListener('resize', function() {
    var wrapper = document.getElementById('preview-sticky-wrapper');
    if (!wrapper) return;
    if (window.innerWidth < 1024) {
        wrapper.style.position = wrapper.style.top = wrapper.style.right = wrapper.style.width = wrapper.style.zIndex = '';
    }
});

// ============================================
// SESSION HANDLER
// ============================================
var shouldOpenProductModal = {{ session('openProductModal') ? 'true' : 'false' }};
document.addEventListener("DOMContentLoaded", function() {
    if (shouldOpenProductModal && currentPageId) setTimeout(openProductModal, 500);
});
</script>
@endsection