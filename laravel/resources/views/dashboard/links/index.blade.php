@extends('layouts.dashboard')

@section('content')
@php
    $user = auth()->user();
    $activePage = $pages->first();
@endphp

<div class="max-w-7xl mx-auto px-4 py-6">
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

    <!-- DUA KOLOM -->
    <div class="flex flex-col lg:flex-row gap-8">

        <!-- KIRI - CONTENT -->
        <div class="lg:w-2/3 space-y-8">

            <!-- YOUR PAGES -->
            <div class="bg-white rounded-xl shadow border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Halaman Saya</h3>
                        <button type="button" onclick="showAddPageForm()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Halaman Baru</span>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($pages as $page)
                        <div class="page-item group bg-gray-50 rounded-lg border border-gray-200 p-4 hover:border-blue-300 hover:bg-blue-50 transition">
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
                                
                                <div class="flex items-center gap-2">
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
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- BLOCK LIST -->
            <div class="bg-white rounded-xl shadow border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="font-bold text-gray-900 text-lg">Daftar Blok</h2>
                </div>

                <!-- ADD BLOCK -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-medium text-gray-900 mb-4">Tambahkan blok baru</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
                                <i class="fas fa-video text-red-600 text-xl mb-2"></i>
                                <span class="text-sm font-medium">Video</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- BLOCK DATA -->
                <div class="p-6">
                    @if($activePage && $activePage->blocks->count())
                    <div id="blockList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($activePage->blocks as $block)
                        <div data-id="{{ $block->id }}"
                            data-type="{{ $block->type }}"
                            data-content='@json($block->content)'
                            onclick="editBlock(this)"
                            class="bg-gray-50 rounded-lg border border-gray-200 p-4 hover:bg-gray-100 transition cursor-pointer">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    @switch($block->type)
                                        @case('text')
                                            <i class="fas fa-font text-blue-600"></i>
                                            @break
                                        @case('image')
                                            <i class="fas fa-image text-green-600"></i>
                                            @break
                                        @case('link')
                                            <i class="fas fa-link text-purple-600"></i>
                                            @break
                                        @case('video')
                                            <i class="fas fa-video text-red-600"></i>
                                            @break
                                    @endswitch
                                    <span class="font-medium">{{ ucfirst($block->type) }} Block</span>
                                </div>
                                <i class="fas fa-grip-vertical text-gray-400"></i>
                            </div>
                            @if($block->content && isset($block->content['text']))
                            <p class="text-sm text-gray-600 truncate">
                                {{ substr($block->content['text'], 0, 40) }}
                            </p>
                            @endif
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
                </div>
            </div>
        </div>

        <!-- KANAN - PREVIEW -->
        <div class="lg:w-1/3 -mt-6">
            <div class="sticky top-0">
                <div class="mb-4">
                    <h3 class="font-bold text-gray-900 mb-2">Preview</h3>
                    <p class="text-sm text-gray-600">Tampilan di mobile device</p>
                </div>
                
                <!-- PHONE FRAME -->
                <div class="relative mx-auto w-[320px]">
                    <!-- Phone Body -->
                    <div class="relative bg-gray-900 rounded-[40px] p-2 shadow-2xl">
                        <!-- Notch -->
                        <div class="h-6 bg-black rounded-b-xl mb-2"></div>
                        
                        <!-- Screen -->
                        <div class="bg-white rounded-[30px] overflow-hidden">
                            <iframe
                                id="preview"
                                src="{{ url('/preview/'.$user->username) }}"
                                class="w-full h-[560px]"
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

<!-- MODAL EDIT PAGE -->
<div id="editPageModal" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="modal-container bg-white rounded-xl shadow-lg w-full max-w-md">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Edit Halaman</h3>
            <button type="button" onclick="closeModal()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editPageForm" method="POST" action="">
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
                    <button type="button" onclick="closeModal()"
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
<div id="addPageModal" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="modal-container bg-white rounded-xl shadow-lg w-full max-w-md">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Tambah Halaman Baru</h3>
            <button type="button" onclick="closeAddModal()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('pages.store') }}" method="POST">
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

<!-- MODAL ADD BLOCK -->
<div id="blockModal" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 id="blockModalTitle" class="font-bold text-lg">Tambah Blok</h3>
            <button onclick="closeBlockModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="blockForm" class="p-6 space-y-4">
            <input type="hidden" id="blockType">

            <!-- TEXT -->
            <div id="textField" class="hidden">
                <label class="block text-sm font-medium mb-1">Teks</label>
                <textarea id="textContent" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3"></textarea>
            </div>

            <!-- LINK -->
            <div id="linkField" class="hidden">
                <label class="block text-sm font-medium mb-1">Judul</label>
                <input id="linkTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <label class="block text-sm font-medium mb-1">URL</label>
                <input id="linkUrl" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- VIDEO -->
            <div id="videoField" class="hidden">
                <label class="block text-sm font-medium mb-1">Link YouTube</label>
                <input id="videoUrl" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- IMAGE -->
            <div id="imageField" class="hidden">
                <label class="block text-sm font-medium mb-1">Upload Gambar</label>
                <input type="file" id="imageFile" accept="image/*">
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeBlockModal()" class="px-4 py-2 rounded-lg btn-secondary">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-lg btn-primary">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

</div>

<style>
/* Reset dan base styles */
.page-item {
    transition: all 0.2s;
}

.modal-overlay {
    backdrop-filter: blur(4px);
}

.modal-container {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Main layout container */
.max-w-7xl {
    max-width: 90rem; /* Lebar maksimum lebih besar */
}

.max-w-7xl.mx-auto.px-4.py-6 {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
}

/* HEADER section */
.mb-8 {
    margin-bottom: 2rem;
}

.text-2xl.font-bold.text-gray-900.mb-2 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}

/* DUA KOLOM layout */
.flex.flex-col.lg\:flex-row.gap-8 {
    gap: 2rem;
}

/* KIRI - CONTENT column (2/3) */
.lg\:w-2\/3 {
    width: 66.666667%;
}

/* KANAN - PREVIEW column (1/3) */
.lg\:w-1\/3 {
    width: 33.333333%;
}

/* Your Pages section */
.bg-white.rounded-xl.shadow.border.border-gray-200 {
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.p-6.border-b.border-gray-200 {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

/* Page items list */
.space-y-4 > * + * {
    margin-top: 0.5rem;
}

.page-item {
    padding: 0.875rem 1rem;
    border-radius: 0.5rem;
}

/* Block List section */
/* Add new block buttons grid */
.grid.grid-cols-2.md\:grid-cols-4.gap-4 {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.75rem;
}

/* Individual block type buttons */
.border.border-gray-300.rounded-xl.p-4 {
    padding: 1rem;
    height: 85px;
    border-radius: 0.75rem;
    border: 1px solid #d1d5db;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.border.border-gray-300.rounded-xl.p-4:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Block type icons */
.link-page .fa-font,
.link-page .fa-image,
.link-page .fa-link,
.link-page .fa-video {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}


/* Block List items container */
#blockList {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 1rem;
    min-height: 200px;
}

/* Individual block items */
#blockList .bg-gray-50 {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1rem;
    cursor: move;
    transition: all 0.2s;
    min-height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

#blockList .bg-gray-50:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

/* Block item content layout */
#blockList .flex.items-center.justify-between.mb-2 {
    margin-bottom: 0.5rem;
}

#blockList .fa-grip-vertical {
    color: #9ca3af;
}

#blockList .text-sm.text-gray-600 {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

/* Empty state for blocks */
.text-center.py-8.text-gray-400.border-2.border-dashed.border-gray-300.rounded-lg {
    padding: 3rem 1rem;
    min-height: 150px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    margin-top: 1rem;
}

/* PHONE FRAME - Preview section */
.sticky.top-6 {
    position: sticky;
    top: 1.5rem;
}

.relative.mx-auto.w-\[320px\] {
    width: 320px;
    margin: 0;
}

.relative.bg-gray-900.rounded-\[40px\].p-2.shadow-2xl {
    border-radius: 2.5rem;
    padding: 0.5rem;
    background: linear-gradient(145deg, #111827, #1f2937);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    border: 8px solid #1f2937;
}

/* Phone notch */
.h-6.bg-black.rounded-b-xl.mb-2 {
    height: 1.25rem;
    background: #000;
    border-bottom-left-radius: 0.75rem;
    border-bottom-right-radius: 0.75rem;
    width: 35%;
    margin: 0 auto 0.5rem;
}

/* Phone screen */
.bg-white.rounded-\[30px\].overflow-hidden {
    border-radius: 1.5rem;
    background: white;
    height: 570px;
    overflow: hidden;
}

/* Iframe */
iframe#preview {
    width: 100%;
    height: 100%;
    border: none;
    display: block;
}

/* Home indicator */
.h-1.w-24.bg-gray-800.rounded-full.mx-auto.mt-2 {
    height: 0.25rem;
    width: 5rem;
    background: #374151;
    border-radius: 9999px;
    margin-top: 0.5rem;
}

/* Modal improvements */
#editPageModal.modal-overlay,
#addPageModal.modal-overlay {
    z-index: 9999;
}

.modal-container.bg-white.rounded-xl.shadow-lg.w-full.max-w-md {
    max-width: 420px;
    border-radius: 1rem;
    overflow: hidden;
}

.modal-container .p-6 {
    padding: 1.25rem 1.5rem;
}

/* Form inputs in modal */
input[type="text"].w-full.px-3.py-2.border.border-gray-300.rounded-lg {
    padding: 0.625rem 0.875rem;
    border-radius: 0.5rem;
    font-size: 0.9375rem;
}

/* Modal buttons */
.flex.justify-end.gap-3 {
    gap: 0.75rem;
}

.px-4.py-2.bg-blue-600.text-white.rounded-lg {
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 500;
}

/* Action buttons in page items */
.flex.items-center.gap-2 {
    gap: 0.375rem;
}

.p-2.text-gray-500.hover\:text-blue-600.hover\:bg-blue-50.rounded-lg {
    padding: 0.375rem;
    border-radius: 0.375rem;
}

/* Responsive design */
@media (max-width: 1024px) {
    .flex.flex-col.lg\:flex-row.gap-8 {
        flex-direction: column;
    }
    
    .lg\:w-2\/3,
    .lg\:w-1\/3 {
        width: 100%;
    }
    
    .relative.mx-auto.w-\[320px\] {
        margin: 0 auto;
    }
    
    .grid.grid-cols-2.md\:grid-cols-4.gap-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .max-w-7xl.mx-auto.px-4.py-6 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .p-6 {
        padding: 1rem;
    }
    
    .grid.grid-cols-2.md\:grid-cols-4.gap-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.5rem;
    }
    
    .border.border-gray-300.rounded-xl.p-4 {
        height: 75px;
        padding: 0.75rem;
    }
}

@media (max-width: 640px) {
    .text-2xl.font-bold.text-gray-900.mb-2 {
        font-size: 1.5rem;
    }
    
    .grid.grid-cols-2.md\:grid-cols-4.gap-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* Drag and drop visual feedback */
.sortable-ghost {
    opacity: 0.4;
    background: #dbeafe;
}

.sortable-drag {
    opacity: 0.8;
    transform: rotate(5deg);
}

/* Preview section text */
.mb-4 h3.font-bold.text-gray-900 {
    font-size: 1.125rem;
    margin-bottom: 0.25rem;
}

.mb-4 p.text-sm.text-gray-600 {
    font-size: 0.8125rem;
    color: #6b7280;
}

/* GLOBAL BUTTON STYLE */
.btn-primary {
    background-color: #2563eb;
    color: white;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background-color: #1d4ed8;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
}

.btn-secondary {
    background-color: white;
    border: 1px solid #d1d5db;
    color: #374151;
    transition: all 0.2s ease;
}

.btn-secondary:hover {
    background-color: #f9fafb;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
}

.btn-danger {
    background-color: #dc2626;
    color: white;
    transition: all 0.2s ease;
}

.btn-danger:hover {
    background-color: #b91c1c;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
// Initialize Sortable for blocks
document.addEventListener('DOMContentLoaded', function() {
    const blockList = document.getElementById('blockList');
    if (blockList) {
        new Sortable(blockList, {
            animation: 150,
            ghostClass: 'bg-blue-50',
            onEnd: function() {
                const order = Array.from(blockList.children).map((item, index) => ({
                    id: item.dataset.id,
                    position: index + 1
                }));
                
                fetch('{{ route("blocks.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(order)
                }).then(() => {
                    document.getElementById('preview').contentWindow.location.reload();
                });
            }
        });
    }
});

// Function to add block
function addBlock(type) {
    const modal = document.getElementById('blockModal');
    const form = document.getElementById('blockForm');

    modal.classList.remove('hidden');
    document.getElementById('blockType').value = type;

    form.removeAttribute('data-edit-id');
    form.reset();
    document.getElementById('blockModalTitle').innerText = "Tambah Blok";

    // hide semua field dulu
    document.getElementById('textField').classList.add('hidden');
    document.getElementById('linkField').classList.add('hidden');
    document.getElementById('videoField').classList.add('hidden');
    document.getElementById('imageField').classList.add('hidden');

    if (type === 'text') document.getElementById('textField').classList.remove('hidden');
    if (type === 'link') document.getElementById('linkField').classList.remove('hidden');
    if (type === 'video') document.getElementById('videoField').classList.remove('hidden');
    if (type === 'image') document.getElementById('imageField').classList.remove('hidden');
}

function closeBlockModal() {
    const form = document.getElementById('blockForm');
    form.removeAttribute('data-edit-id');
    form.reset();
    document.getElementById('blockModal').classList.add('hidden');
} 

// Edit block
function editBlock(element) {
    const id = element.dataset.id;
    const type = element.dataset.type;
    const content = JSON.parse(element.dataset.content);

    const modal = document.getElementById('blockModal');
    modal.classList.remove('hidden');

    document.getElementById('blockType').value = type;
    document.getElementById('blockForm').dataset.editId = id;

    // reset semua field
    document.getElementById('textField').classList.add('hidden');
    document.getElementById('linkField').classList.add('hidden');
    document.getElementById('videoField').classList.add('hidden');
    document.getElementById('imageField').classList.add('hidden');

    if (type === 'text') {
        document.getElementById('textField').classList.remove('hidden');
        document.getElementById('textContent').value = content.text || '';
    }

    if (type === 'link') {
        document.getElementById('linkField').classList.remove('hidden');
        document.getElementById('linkTitle').value = content.title || '';
        document.getElementById('linkUrl').value = content.url || '';
    }

    if (type === 'video') {
        document.getElementById('videoField').classList.remove('hidden');
        document.getElementById('videoUrl').value = content.url || '';
    }

    if (type === 'image') {
        document.getElementById('imageField').classList.remove('hidden');
    }

    document.getElementById('blockModalTitle').innerText = "Edit Blok";
}

// Show Edit Modal
function showEditModal(pageId, pageTitle) {
    const modal = document.getElementById('editPageModal');
    const form = document.getElementById('editPageForm');
    const titleInput = document.getElementById('edit_page_title');
    const pageIdInput = document.getElementById('edit_page_id');
    
    // Set form action
    form.action = `/pages/${pageId}`;
    
    // Set input values
    pageIdInput.value = pageId;
    titleInput.value = pageTitle;
    
    // Show modal
    modal.classList.remove('hidden');
    titleInput.focus();
}

// Show Add Page Modal
function showAddPageForm() {
    document.getElementById('addPageModal').classList.remove('hidden');
    setTimeout(() => {
        document.getElementById('new_page_title').focus();
    }, 100);
}

// Close Edit Modal
function closeModal() {
    document.getElementById('editPageModal').classList.add('hidden');
}

// Close Add Modal
function closeAddModal() {
    document.getElementById('addPageModal').classList.add('hidden');
}

// Confirm Delete
function confirmDelete(button) {
    if (confirm('Yakin ingin menghapus page ini?')) {
        button.closest('form').submit();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        closeModal();
        closeAddModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeAddModal();
    }
});

// Handle edit form submission
document.getElementById('editPageForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        } else {
            alert('Gagal mengupdate page');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
});

const form = document.getElementById('blockForm');

form.onsubmit = function(e) {
    e.preventDefault();

    const editId = form.getAttribute('data-edit-id');
    const type = document.getElementById('blockType').value;

    let formData = new FormData();
    formData.append('type', type);

    let url = '';

    // =====================
    // MODE EDIT
    // =====================
    if (editId) {
        url = '/blocks/' + editId;
        formData.append('_method', 'PUT');
    } 
    // =====================
    // MODE TAMBAH
    // =====================
    else {
        url = '/blocks';
        formData.append('page_id', {{ $activePage->id ?? 'null' }});
    }

    // FIELD TEXT
    if (type === 'text') {
        formData.append('content[text]', document.getElementById('textContent').value);
    }

    // FIELD LINK
    if (type === 'link') {
        formData.append('content[title]', document.getElementById('linkTitle').value);
        formData.append('content[url]', document.getElementById('linkUrl').value);
    }

    // FIELD VIDEO
    if (type === 'video') {
        formData.append('content[url]', document.getElementById('videoUrl').value);
    }

    // FIELD IMAGE
    if (type === 'image') {
        const file = document.getElementById('imageFile').files[0];
        if (file) {
            formData.append('image', file);
        }
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(() => {
        form.removeAttribute('data-edit-id');
        location.reload();
    });
};

function reloadPreview() {
    const iframe = document.getElementById('preview');
    iframe.src = iframe.src;
}


document.addEventListener('click', function (e) {
    if (e.target.classList.contains('delete-block')) {
        const id = e.target.dataset.id;

        if (!confirm('Yakin hapus block ini?')) return;

        fetch(`/blocks/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                e.target.closest('.block-item').remove();
            }
        });
    }
});


</script>
@endsection