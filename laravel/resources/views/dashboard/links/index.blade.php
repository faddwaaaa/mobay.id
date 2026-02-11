@extends('layouts.dashboard')

@section('content')
@php
    $user = auth()->user();
    // Ambil page_id dari query parameter atau gunakan page pertama
    $selectedPageId = request()->query('page');
    $activePage = $selectedPageId 
        ? $pages->where('id', $selectedPageId)->first() 
        : $pages->first();
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
                            <span>Tambah Halaman</span>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    @if($pages->count() > 0)
                    <div class="space-y-4">
                        @foreach($pages as $page)
                        <div class="page-item group rounded-lg border p-4 transition cursor-pointer {{ $activePage && $activePage->id == $page->id ? 'bg-blue-50 border-blue-300' : 'bg-gray-50 border-gray-200 hover:border-blue-300 hover:bg-blue-50' }}"
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
                    @else
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-folder-open text-3xl mb-3"></i>
                        <p class="font-medium">Belum ada halaman</p>
                        <p class="text-sm mt-1">Halaman "Home" otomatis dibuat saat registrasi</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- BLOCK LIST -->
            <div class="bg-white rounded-xl shadow border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="font-bold text-gray-900 text-lg">Daftar Blok</h2>
                    <div class="flex justify-between items-center">
                        <h2 class="font-bold text-gray-900">
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
                                                            <i class="fas fa-video text-red-600"></i>
                                                            <span class="font-medium text-gray-900">Video</span>
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
                                                    <img src="{{ asset('storage/' . $block->content['image']) }}" 
                                                         class="w-20 h-20 object-cover rounded mt-1"
                                                         alt="Block image">
                                                @elseif($block->type === 'video' && isset($block->content['video']))
                                                    <div class="mt-1">
                                                        <video class="w-20 h-20 object-cover rounded" controls>
                                                            <source src="{{ asset('storage/' . $block->content['video']) }}" type="video/mp4">
                                                        </video>
                                                        <p class="text-xs text-gray-400 mt-1">Video file</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-1">
                                            <button type="button" 
                                                    onclick="editBlock({{ $block->id }}, '{{ $block->type }}', {{ json_encode($block->content) }})"
                                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
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
            <input type="hidden" id="blockId">
            <input type="hidden" id="isEdit" value="0">

            <!-- TEXT -->
            <div id="textField" class="hidden">
                <label class="block text-sm font-medium mb-1">Teks</label>
                <textarea id="textContent" class="w-full border rounded-lg p-2" rows="3" placeholder="Masukkan teks Anda..."></textarea>
            </div>

            <!-- LINK -->
            <div id="linkField" class="hidden">
                <label class="block text-sm font-medium mb-1">Judul</label>
                <input id="linkTitle" class="w-full border rounded-lg p-2 mb-2" placeholder="Nama link">
                <label class="block text-sm font-medium mb-1">URL</label>
                <input id="linkUrl" class="w-full border rounded-lg p-2" placeholder="https://example.com">
            </div>

            <!-- VIDEO -->
            <div id="videoField" class="hidden">
                <label class="block text-sm font-medium mb-1">Upload Video</label>
                <input type="file" id="videoFile" accept="video/*" class="w-full border rounded-lg p-2">
                <p class="text-xs text-gray-500 mt-1">MP4, AVI, MOV (Max 50MB)</p>
            </div>

            <!-- IMAGE -->
            <div id="imageField" class="hidden">
                <label class="block text-sm font-medium mb-1">Upload Gambar</label>
                <input type="file" id="imageFile" accept="image/*" class="w-full border rounded-lg p-2">
                <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG (Max 5MB)</p>
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

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
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

/* Sortable styles */
.sortable-ghost {
    opacity: 0.4;
    background: #dbeafe;
}

.sortable-drag {
    opacity: 0.8;
    transform: rotate(2deg);
}

/* Responsive */
@media (max-width: 1024px) {
    .lg\:w-2\/3,
    .lg\:w-1\/3 {
        width: 100%;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
let currentPageId = {{ $activePage->id ?? 'null' }};

// Initialize Sortable for blocks
document.addEventListener('DOMContentLoaded', function() {
    const blockList = document.getElementById('blockList');
    if (blockList) {
        new Sortable(blockList, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            handle: '.fa-grip-vertical',
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
                    body: JSON.stringify({ order: order })
                }).then(() => {
                    document.getElementById('preview').contentWindow.location.reload();
                });
            }
        });
    }
});

// Select page
function selectPage(pageId) {
    window.location.href = `{{ route('links.index') }}?page=${pageId}`;
}

// Function to add block
function addBlock(type) {
    document.getElementById('blockModal').classList.remove('hidden');
    document.getElementById('blockModalTitle').textContent = 'Tambah Blok';
    document.getElementById('blockType').value = type;
    document.getElementById('isEdit').value = '0';
    document.getElementById('blockId').value = '';
    document.getElementById('submitBtnText').textContent = 'Simpan';

    // Hide all fields
    document.getElementById('textField').classList.add('hidden');
    document.getElementById('linkField').classList.add('hidden');
    document.getElementById('videoField').classList.add('hidden');
    document.getElementById('imageField').classList.add('hidden');

    document.getElementById('textContent').value = '';
    document.getElementById('linkTitle').value = '';
    document.getElementById('linkUrl').value = '';
    document.getElementById('videoFile').value = '';
    document.getElementById('imageFile').value = '';

    // Show relevant field
    if (type === 'text') document.getElementById('textField').classList.remove('hidden');
    if (type === 'link') document.getElementById('linkField').classList.remove('hidden');
    if (type === 'video') document.getElementById('videoField').classList.remove('hidden');
    if (type === 'image') document.getElementById('imageField').classList.remove('hidden');
}

// Function to edit block
function editBlock(blockId, type, content) {
    document.getElementById('blockModal').classList.remove('hidden');
    document.getElementById('blockModalTitle').textContent = 'Edit Blok';
    document.getElementById('blockType').value = type;
    document.getElementById('isEdit').value = '1';
    document.getElementById('blockId').value = blockId;
    document.getElementById('submitBtnText').textContent = 'Update';

    // Hide all fields
    document.getElementById('textField').classList.add('hidden');
    document.getElementById('linkField').classList.add('hidden');
    document.getElementById('videoField').classList.add('hidden');
    document.getElementById('imageField').classList.add('hidden');

    // Fill data based on type
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
        // Note: file input can't be prefilled for security reasons
    }
    
    if (type === 'image') {
        document.getElementById('imageField').classList.remove('hidden');
        // Note: file input can't be prefilled for security reasons
    }
}

// Function to delete block
function deleteBlock(blockId) {
    if (!confirm('Yakin ingin menghapus blok ini?')) return;

    fetch(`/blocks/${blockId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal menghapus blok');
        }
    });
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
        closeBlockModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeAddModal();
        closeBlockModal();
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

// Handle block form submission
document.getElementById('blockForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!currentPageId) {
        alert('Silakan pilih halaman terlebih dahulu');
        return;
    }

    const type = document.getElementById('blockType').value;
    const isEdit = document.getElementById('isEdit').value === '1';
    const blockId = document.getElementById('blockId').value;
    const formData = new FormData();

    formData.append('page_id', currentPageId);
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
        const videoFile = document.getElementById('videoFile').files[0];
        if (videoFile) {
            formData.append('video', videoFile);
        } else if (!isEdit) {
            alert('Silakan pilih video');
            return;
        }
    }

    // FIELD IMAGE
    if (type === 'image') {
        const imageFile = document.getElementById('imageFile').files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        } else if (!isEdit) {
            alert('Silakan pilih gambar');
            return;
        }
    }

    const url = isEdit ? `/blocks/${blockId}` : '{{ route("blocks.store") }}';
    if (isEdit) {
        formData.append('_method', 'PUT');
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    }).then(res => {
        if (res.ok) {
            closeBlockModal();
            location.reload();
        } else {
            alert('Gagal menyimpan blok');
        }
    });
});
</script>
@endsection