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
                                                            {{-- Badge edit produk --}}
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
                                                    {{-- Live fetch dari API agar selalu sinkron --}}
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
                                            {{-- Tombol edit: produk → buka edit produk modal / halaman; lainnya → edit blok --}}
                                            @if($block->type === 'product' && isset($block->product_id) && $block->product_id)
                                                <button type="button"
                                                        onclick="openEditProductFromBlock({{ $block->product_id }})"
                                                        class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition"
                                                        title="Edit produk ini">
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

<!-- ============================================================
     MODAL EDIT PRODUK (dari blok)
     Muncul di atas halaman ini tanpa pindah halaman
============================================================ -->
<div id="editProductModal"
     class="fixed inset-0 z-[9999] hidden items-center justify-center p-4"
     style="display:none;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         onclick="closeEditProductModal()"></div>

    <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl max-h-[92vh] flex flex-col"
         style="animation: editModalIn .22s cubic-bezier(.16,1,.3,1);">

        {{-- Header --}}
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-yellow-50 rounded-lg">
                    <i class="fas fa-box text-yellow-500 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Produk</h2>
                    <p class="text-xs text-gray-500 mt-0.5" id="editProductModalSubtitle">Memuat data produk...</p>
                </div>
            </div>
            <button onclick="closeEditProductModal()"
                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Loading state --}}
        <div id="editProductModalLoading" class="flex-1 flex items-center justify-center py-16">
            <div class="text-center text-gray-400">
                <div class="w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                <p class="text-sm">Memuat data produk...</p>
            </div>
        </div>

        {{-- Form (disuntikkan via JS) --}}
        <div id="editProductModalBody" class="hidden p-5 overflow-y-auto flex-1"></div>

        {{-- Footer --}}
        <div id="editProductModalFooter"
             class="hidden p-5 border-t border-gray-100 flex items-center justify-between gap-3 bg-gray-50/60 rounded-b-2xl">
            <p class="text-xs text-gray-400 flex items-center gap-1">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xs"></i>
                Perubahan langsung tersinkron ke preview
            </p>
            <div class="flex gap-2">
                <button type="button"
                        onclick="closeEditProductModal()"
                        class="px-4 py-2 text-sm font-medium bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">
                    Batal
                </button>
                <button type="button"
                        onclick="submitEditProduct()"
                        class="px-5 py-2 text-sm font-semibold bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg shadow-sm hover:shadow-md transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-check text-xs"></i>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

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

<!-- MODAL PRODUK -->
<div id="productModal" class="modal fixed inset-0 z-[200] flex items-center justify-center p-4 hidden">
    <div class="bg-white p-6 rounded-xl w-[450px] relative shadow-xl">
        <button type="button" onclick="closeProductModal()" 
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>

        <h2 class="text-lg font-bold mb-4">Pilih Produk</h2>

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

@keyframes editModalIn {
    from { opacity: 0; transform: scale(.96) translateY(8px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
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

/* Edit product modal input styles */
#editProductModalBody input,
#editProductModalBody textarea,
#editProductModalBody select {
    width: 100%; padding: 9px 12px;
    border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: 13px; transition: all .2s; background: white; color: #1f2937;
}
#editProductModalBody input:focus,
#editProductModalBody textarea:focus,
#editProductModalBody select:focus {
    outline: none; border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.1);
}
#editProductModalBody label {
    display: block; font-size: 12px; font-weight: 600;
    color: #374151; margin-bottom: 5px;
}
.epm-card {
    background: white; border-radius: 12px;
    border: 1.5px solid rgba(229,231,235,0.7);
    box-shadow: 0 1px 4px rgba(0,0,0,0.04); padding: 16px;
    margin-bottom: 16px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
// ============================================
// LIVE FETCH PRODUCT INFO IN BLOCK LIST
// Render thumbnail + title + harga dari API, bukan dari snapshot
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
                    ? `<span style="font-size:11px;color:#dc2626;font-weight:600;">Rp ${fmtNum(final)}</span>
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

// ============================================
// GLOBAL VARIABLES
// ============================================
let currentPageId = {{ $activePage->id ?? 'null' }};
let _editingProductId = null;

// ============================================
// EDIT PRODUK DARI BLOK
// ============================================
async function openEditProductFromBlock(productId) {
    _editingProductId = productId;

    const modal   = document.getElementById('editProductModal');
    const loading = document.getElementById('editProductModalLoading');
    const body    = document.getElementById('editProductModalBody');
    const footer  = document.getElementById('editProductModalFooter');
    const subtitle= document.getElementById('editProductModalSubtitle');

    // Reset & show
    body.classList.add('hidden');
    footer.classList.add('hidden');
    loading.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    try {
        const res  = await fetch(`/api/product/${productId}`);
        const prod = await res.json();

        subtitle.textContent = prod.title;

        // Harga: jika tersimpan sebagai format titik, bersihkan dulu
        const priceVal    = prod.price ?? 0;
        const discountVal = (prod.discount && prod.discount > 0) ? prod.discount : '';

        body.innerHTML = `
            <div class="epm-card">
                <h3 style="font-size:13px;font-weight:600;color:#374151;margin-bottom:12px;">Gambar Produk</h3>

                ${prod.image_url ? `
                <div style="margin-bottom:12px;">
                    <p style="font-size:11px;font-weight:600;color:#6b7280;margin-bottom:6px;">Gambar Saat Ini</p>
                    <img src="${prod.image_url}" alt=""
                         style="width:100%;height:140px;object-fit:cover;border-radius:8px;border:1.5px solid #e5e7eb;">
                </div>` : ''}

                <label>Ganti Gambar <span style="font-weight:400;color:#9ca3af;">(opsional)</span></label>
                <div id="epm_image_drop"
                     style="border:2px dashed #e5e7eb;border-radius:10px;padding:16px;text-align:center;cursor:pointer;transition:border-color .2s;"
                     onmouseenter="this.style.borderColor='#3b82f6'"
                     onmouseleave="this.style.borderColor='#e5e7eb'">
                    <input type="file" id="epm_image_file" accept="image/*" style="display:none;"
                           onchange="epmPreviewImage(this)">
                    <label for="epm_image_file" style="cursor:pointer;display:block;">
                        <div style="width:36px;height:36px;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                            <svg width="18" height="18" fill="none" stroke="#3b82f6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <p style="font-size:13px;color:#374151;font-weight:500;">Klik untuk upload gambar baru</p>
                        <p style="font-size:11px;color:#9ca3af;margin-top:2px;">PNG, JPG, JPEG (Max 5MB)</p>
                    </label>
                </div>
                <div id="epm_image_preview" style="display:none;margin-top:10px;">
                    <p style="font-size:11px;font-weight:600;color:#6b7280;margin-bottom:6px;">Preview Gambar Baru</p>
                    <div style="position:relative;display:inline-block;width:100%;">
                        <img id="epm_image_preview_img" src="" alt=""
                             style="width:100%;height:140px;object-fit:cover;border-radius:8px;border:1.5px solid #3b82f6;">
                        <button type="button" onclick="epmClearImage()"
                                style="position:absolute;top:6px;right:6px;width:24px;height:24px;background:#ef4444;border:none;border-radius:50%;cursor:pointer;color:white;font-size:12px;display:flex;align-items:center;justify-content:center;">✕</button>
                    </div>
                </div>
            </div>

            <div class="epm-card">
                <h3 style="font-size:13px;font-weight:600;color:#374151;margin-bottom:12px;">Informasi Produk</h3>
                <div style="margin-bottom:12px;">
                    <label>Judul Produk</label>
                    <input type="text" id="epm_title" value="${escHtml(prod.title)}">
                </div>
                <div>
                    <label>Deskripsi</label>
                    <textarea id="epm_description" rows="3">${escHtml(prod.description ?? '')}</textarea>
                </div>
            </div>
            <div class="epm-card">
                <h3 style="font-size:13px;font-weight:600;color:#374151;margin-bottom:12px;">Harga & Diskon</h3>
                <div style="margin-bottom:12px;">
                    <label>Harga Normal (Rp)</label>
                    <input type="text" id="epm_price"
                           value="${formatRupiahVal(priceVal)}"
                           oninput="epmFormatRupiah(this)"
                           placeholder="Contoh: 100.000">
                    <p style="font-size:11px;color:#9ca3af;margin-top:4px;">Harga otomatis diformat</p>
                </div>
                <div>
                    <label>Harga Setelah Diskon <span style="font-weight:400;color:#9ca3af;">(opsional)</span></label>
                    <input type="text" id="epm_discount"
                           value="${discountVal ? formatRupiahVal(discountVal) : ''}"
                           oninput="epmFormatRupiah(this)"
                           placeholder="Kosongkan jika tidak ada diskon">
                    <p style="font-size:11px;color:#3b82f6;margin-top:4px;">
                        Masukkan harga setelah diskon, bukan persentase
                    </p>
                    <div id="epm_discount_preview" style="display:none;margin-top:8px;padding:6px 10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;">
                        <p style="font-size:11px;color:#16a34a;">
                            <strong>Hemat:</strong> <span id="epm_saved_amount"></span>
                            <span id="epm_saved_pct" style="margin-left:4px;"></span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="epm-card">
                <h3 style="font-size:13px;font-weight:600;color:#374151;margin-bottom:12px;">Stok & Batas Pembelian</h3>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div>
                        <p style="font-size:13px;font-weight:500;color:#374151;">Kelola Stok</p>
                        <p style="font-size:11px;color:#9ca3af;">Aktifkan untuk mengatur jumlah stok</p>
                    </div>
                    <label style="position:relative;display:inline-block;width:44px;height:24px;margin:0;">
                        <input type="checkbox" id="epm_stock_toggle"
                               ${prod.stock !== null && prod.stock !== undefined ? 'checked' : ''}
                               onchange="epmToggle(this,'epm_stock_input')"
                               style="opacity:0;width:0;height:0;position:absolute;">
                        <span class="epm-toggle-track" id="epm_stock_track"></span>
                    </label>
                </div>
                <input type="number" id="epm_stock_input"
                       value="${prod.stock ?? ''}"
                       placeholder="Jumlah stok tersedia" min="1"
                       style="${prod.stock !== null && prod.stock !== undefined ? '' : 'display:none;'}margin-bottom:12px;">

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;padding-top:12px;border-top:1px solid #f3f4f6;">
                    <div>
                        <p style="font-size:13px;font-weight:500;color:#374151;">Batas Pembelian</p>
                        <p style="font-size:11px;color:#9ca3af;">Batasi pembelian per pengguna</p>
                    </div>
                    <label style="position:relative;display:inline-block;width:44px;height:24px;margin:0;">
                        <input type="checkbox" id="epm_limit_toggle"
                               ${prod.purchase_limit !== null && prod.purchase_limit !== undefined ? 'checked' : ''}
                               onchange="epmToggle(this,'epm_limit_input')"
                               style="opacity:0;width:0;height:0;position:absolute;">
                        <span class="epm-toggle-track" id="epm_limit_track"></span>
                    </label>
                </div>
                <input type="number" id="epm_limit_input"
                       value="${prod.purchase_limit ?? ''}"
                       placeholder="Maksimal beli per user" min="1"
                       style="${prod.purchase_limit !== null && prod.purchase_limit !== undefined ? '' : 'display:none;'}">
            </div>
        `;

        // Init toggle visual
        epmInitToggle('epm_stock_toggle', 'epm_stock_track');
        epmInitToggle('epm_limit_toggle', 'epm_limit_track');

        // Discount preview listener
        document.getElementById('epm_price').addEventListener('input', epmUpdateDiscountPreview);
        document.getElementById('epm_discount').addEventListener('input', epmUpdateDiscountPreview);
        epmUpdateDiscountPreview();

        loading.classList.add('hidden');
        body.classList.remove('hidden');
        footer.classList.remove('hidden');

    } catch (e) {
        loading.innerHTML = `<div class="text-center py-8 text-red-500">
            <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
            <p class="text-sm">Gagal memuat produk. Coba lagi.</p>
        </div>`;
    }
}

function closeEditProductModal() {
    document.getElementById('editProductModal').style.display = 'none';
    document.body.style.overflow = '';
    _editingProductId = null;
}

async function submitEditProduct() {
    if (!_editingProductId) return;

    const title       = document.getElementById('epm_title')?.value.trim() ?? '';
    const description = document.getElementById('epm_description')?.value.trim() ?? '';
    const priceRaw    = (document.getElementById('epm_price')?.value ?? '').replace(/\./g,'').replace(/,/g,'');
    const discRaw     = (document.getElementById('epm_discount')?.value ?? '').replace(/\./g,'').replace(/,/g,'');
    const stockToggle = document.getElementById('epm_stock_toggle')?.checked ?? false;
    const stock       = stockToggle ? (document.getElementById('epm_stock_input')?.value ?? '') : '';
    const limitToggle = document.getElementById('epm_limit_toggle')?.checked ?? false;
    const limit       = limitToggle ? (document.getElementById('epm_limit_input')?.value ?? '') : '';
    const imageFile   = document.getElementById('epm_image_file')?.files[0] ?? null;

    if (!title) { alert('Judul produk tidak boleh kosong'); return; }
    const priceInt = parseInt(priceRaw);
    if (!priceRaw || isNaN(priceInt) || priceInt <= 0) {
        alert('Harga produk harus lebih dari 0'); return;
    }
    const discInt = discRaw ? parseInt(discRaw) : 0;
    if (discInt > 0 && discInt >= priceInt) {
        alert('Harga diskon harus lebih rendah dari harga normal'); return;
    }

    const btn = document.querySelector('#editProductModalFooter button:last-child');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Menyimpan...';

    try {
        const formData = new FormData();
        // Laravel method spoofing untuk PUT
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('title', title);
        formData.append('description', description);
        formData.append('price', priceInt);
        // Kirim discount: jika kosong kirim 0 agar backend bisa clear
        formData.append('discount', discInt > 0 ? discInt : 0);
        // Stock: kirim nilai atau kosong (backend harus handle null)
        if (stockToggle && stock) {
            formData.append('stock', parseInt(stock));
        }
        // Purchase limit
        if (limitToggle && limit) {
            formData.append('purchase_limit', parseInt(limit));
        }
        // Gambar baru (opsional)
        if (imageFile) {
            formData.append('images[]', imageFile);
        }

        const res = await fetch(`/products/${_editingProductId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData,
            redirect: 'manual'   // jangan ikuti redirect otomatis — tangani sendiri
        });

        // Laravel update biasanya redirect (302) atau return JSON
        // fetch dengan redirect:'manual' → status 0 (opaqueredirect) jika redirect
        const isSuccess = res.ok || res.status === 0 || res.status === 302 || res.status === 200;

        // Coba parse JSON untuk mendapat pesan error detail
        let data = {};
        const contentType = res.headers.get('content-type') ?? '';
        if (contentType.includes('application/json') && res.status !== 0) {
            data = await res.json().catch(() => ({}));
        }

        if (isSuccess && res.status !== 422) {
            closeEditProductModal();
            // Refresh preview iframe
            const iframe = document.getElementById('preview');
            if (iframe) {
                const src = iframe.src.replace(/[&?]t=\d+/, '');
                iframe.src = src + (src.includes('?') ? '&' : '?') + 't=' + Date.now();
            }
            // Re-fetch semua product info di block list
            loadBlockProductInfos();
            showBuilderToast('✓ Produk berhasil diperbarui!', 'success');
        } else if (res.status === 422) {
            // Validation errors dari Laravel
            const errors = data.errors ?? {};
            const msgs   = Object.values(errors).flat();
            alert('Validasi gagal:\n' + (msgs.length ? msgs.join('\n') : JSON.stringify(data)));
        } else {
            alert('Gagal menyimpan (HTTP ' + res.status + '): ' + (data.message || 'Terjadi kesalahan'));
        }
    } catch (e) {
        console.error('submitEditProduct error:', e);
        alert('Terjadi kesalahan jaringan: ' + e.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check text-xs"></i> Simpan Perubahan';
    }
}

// ── Helpers for edit product modal ──
function escHtml(str) {
    if (!str) return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function formatRupiahVal(num) {
    return new Intl.NumberFormat('id-ID').format(parseInt(num));
}

function epmPreviewImage(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('epm_image_preview_img').src = e.target.result;
        document.getElementById('epm_image_preview').style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function epmClearImage() {
    document.getElementById('epm_image_file').value = '';
    document.getElementById('epm_image_preview').style.display = 'none';
    document.getElementById('epm_image_preview_img').src = '';
}

function epmFormatRupiah(input) {
    let angka = input.value.replace(/[^0-9]/g, '');
    if (!angka) { input.value = ''; return; }
    input.value = new Intl.NumberFormat('id-ID').format(angka);
}

function epmToggle(checkbox, inputId) {
    const input = document.getElementById(inputId);
    const track = document.getElementById(inputId.replace('_input', '_track'));
    if (checkbox.checked) {
        input.style.display = '';
        if (track) track.style.background = '#3b82f6';
    } else {
        input.style.display = 'none';
        input.value = '';
        if (track) track.style.background = '#e5e7eb';
    }
    // Move knob
    if (track) {
        const knob = track.querySelector('span');
        if (knob) knob.style.transform = checkbox.checked ? 'translateX(20px)' : 'translateX(0)';
    }
}

function epmInitToggle(checkboxId, trackId) {
    const checkbox = document.getElementById(checkboxId);
    const track    = document.getElementById(trackId);
    if (!track) return;

    track.style.cssText = `
        display:block; width:44px; height:24px;
        background:${checkbox.checked ? '#3b82f6' : '#e5e7eb'};
        border-radius:999px; position:absolute; top:0; left:0;
        cursor:pointer; transition:background .2s;
    `;
    const knob = document.createElement('span');
    knob.style.cssText = `
        position:absolute; top:2px; left:2px;
        width:20px; height:20px; background:white;
        border-radius:50%; transition:transform .2s;
        box-shadow:0 1px 3px rgba(0,0,0,.15);
        transform:${checkbox.checked ? 'translateX(20px)' : 'translateX(0)'};
    `;
    track.appendChild(knob);
    track.addEventListener('click', () => {
        checkbox.checked = !checkbox.checked;
        epmToggle(checkbox, checkboxId.replace('_toggle', '_input'));
    });
}

function epmUpdateDiscountPreview() {
    const priceEl = document.getElementById('epm_price');
    const discEl  = document.getElementById('epm_discount');
    const preview = document.getElementById('epm_discount_preview');
    if (!priceEl || !discEl || !preview) return;

    const price = parseInt(priceEl.value.replace(/\./g,'')) || 0;
    const disc  = parseInt(discEl.value.replace(/\./g,'')) || 0;

    if (price > 0 && disc > 0 && disc < price) {
        const saved = price - disc;
        const pct   = Math.round((saved / price) * 100);
        document.getElementById('epm_saved_amount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(saved);
        document.getElementById('epm_saved_pct').textContent    = `(${pct}% OFF)`;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

// Toast untuk builder page
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
function openProductModal() {
    if (!currentPageId) { alert('Silakan pilih halaman terlebih dahulu'); return; }
    showModal('productModal');
}

function closeProductModal() { closeAllModals(); }

function selectProduct(productId, productData) {
    if (!currentPageId) { alert('Silakan pilih halaman terlebih dahulu'); closeProductModal(); return; }
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
    .then(data => { if (data.success) { closeProductModal(); location.reload(); } else alert('Gagal menambahkan produk'); })
    .catch(() => alert('Terjadi kesalahan saat menambahkan produk'));
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
        if (f)         { formData.append('image', f); }
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
    // Fetch live product info untuk semua blok produk
    loadBlockProductInfos();
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