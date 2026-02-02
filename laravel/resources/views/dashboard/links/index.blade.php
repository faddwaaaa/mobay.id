@extends('layouts.dashboard')

@section('content')
@php
    $user = auth()->user();
    $activePage = $pages->first();
@endphp

<div class="max-w-6xl mx-auto">

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Link Saya</h1>
        <p class="text-gray-600">
            Link Saya:
            <span class="text-blue-600 font-medium">
                {{ url('/' . $user->username) }}
            </span>
        </p>
    </div>

    <!-- DUA KOLOM -->
    <div class="flex flex-col lg:flex-row gap-6">

        <!-- KIRI -->
        <div class="lg:w-2/3 space-y-6">

            <!-- YOUR PAGES -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Your Pages</h3>
                    <button type="button" onclick="showAddPageForm()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>Add new page</span>
                    </button>
                </div>
                
                <div class="space-y-3">
                    @foreach($pages as $page)
                    <div class="page-item group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-link text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $page->title }}</p>
                                    <p class="text-sm text-gray-500">/{{ $page->slug }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        onclick="showEditModal({{ $page->id }}, '{{ $page->title }}')">
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

            <!-- BLOCK LIST -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="font-bold text-gray-900">Block List</h2>
                </div>

                <!-- ADD BLOCK -->
                <div class="p-6 border-b">
                    <h3 class="font-medium text-gray-900 mb-3">Add new block</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button type="button" onclick="addBlock('text')" class="border rounded-lg p-4 hover:bg-blue-50 transition">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-font text-blue-600"></i>
                                <span class="text-sm">Text</span>
                            </div>
                        </button>
                        <button type="button" onclick="addBlock('image')" class="border rounded-lg p-4 hover:bg-blue-50 transition">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-image text-green-600"></i>
                                <span class="text-sm">Image</span>
                            </div>
                        </button>
                        <button type="button" onclick="addBlock('link')" class="border rounded-lg p-4 hover:bg-blue-50 transition">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-link text-purple-600"></i>
                                <span class="text-sm">Link</span>
                            </div>
                        </button>
                        <button type="button" onclick="addBlock('video')" class="border rounded-lg p-4 hover:bg-blue-50 transition">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-video text-red-600"></i>
                                <span class="text-sm">Video</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- BLOCK DATA -->
                <div class="p-6">
                    @if($activePage && $activePage->blocks->count())
                    <ul id="blockList" class="space-y-3">
                        @foreach($activePage->blocks as $block)
                        <li data-id="{{ $block->id }}" 
                            class="p-4 border rounded-lg bg-gray-50 cursor-move hover:bg-gray-100 transition">
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
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center py-8 text-gray-400 border-2 border-dashed rounded-lg">
                        <i class="fas fa-cubes text-3xl mb-3"></i>
                        <p class="font-medium">Belum ada block</p>
                        <p class="text-sm mt-1">Tambahkan block untuk menampilkan konten</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- KANAN - PREVIEW -->
        <div class="lg:w-1/3">
            <div class="sticky top-6">
                <div class="mb-4">
                    <h3 class="font-bold text-gray-900 mb-2">Preview</h3>
                    <p class="text-sm text-gray-600">Tampilan di mobile device</p>
                </div>
                
                <div class="relative mx-auto w-[280px] h-[560px] rounded-[36px] border-[10px] border-gray-900 shadow-2xl bg-black overflow-hidden">
                    <!-- NOTCH -->
                    <div class="h-6 bg-black flex justify-center items-end">
                        <div class="w-20 h-1.5 bg-gray-700 rounded-full mb-1"></div>
                    </div>
                    
                    <!-- IFRAME -->
                    <iframe
                        id="preview"
                        src="{{ url('/preview/'.$user->username) }}"
                        class="w-full h-full bg-white"
                        frameborder="0">
                    </iframe>
                    
                    <!-- HOME INDICATOR -->
                    <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2">
                        <div class="w-24 h-1 bg-gray-800 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT PAGE -->
<div id="editPageModal" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="modal-container bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <div class="modal-header p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Edit Page</h3>
            <button type="button" onclick="closeModal()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <form id="editPageForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="modal-body p-6">
                <input type="hidden" name="page_id" id="edit_page_id">
                <div class="space-y-4">
                    <div>
                        <label for="edit_page_title" class="block text-sm font-medium text-gray-700 mb-2">
                            Page Title
                        </label>
                        <input type="text" 
                               id="edit_page_title" 
                               name="title"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="Masukkan nama page"
                               required>
                        <p class="text-xs text-gray-500 mt-2">
                            Nama ini akan ditampilkan di dashboard dan URL
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer p-6 border-t bg-gray-50 rounded-b-2xl">
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()"
                            class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD PAGE -->
<div id="addPageModal" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="modal-container bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <div class="modal-header p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Add New Page</h3>
            <button type="button" onclick="closeAddModal()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <form action="{{ route('pages.store') }}" method="POST">
            @csrf
            <div class="modal-body p-6">
                <div class="space-y-4">
                    <div>
                        <label for="new_page_title" class="block text-sm font-medium text-gray-700 mb-2">
                            Page Title
                        </label>
                        <input type="text" 
                               id="new_page_title" 
                               name="title"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="Masukkan nama page baru"
                               required>
                        <p class="text-xs text-gray-500 mt-2">
                            Nama ini akan ditampilkan di dashboard dan URL
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer p-6 border-t bg-gray-50 rounded-b-2xl">
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeAddModal()"
                            class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Buat Page
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.page-item {
    padding: 16px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    transition: all 0.2s;
    cursor: pointer;
}

.page-item:hover {
    border-color: #3b82f6;
    background-color: #f8fafc;
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
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animasi untuk tombol edit/delete */
.page-item .opacity-0 {
    transition: opacity 0.2s;
}

.page-item:hover .opacity-0 {
    opacity: 1 !important;
}
</style>

<!-- SORTABLE JS -->
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
    
    // Debug info
    console.log('Page loaded. Forms:');
    document.querySelectorAll('form').forEach((form, i) => {
        console.log(`Form ${i}: ${form.action} (${form.method})`);
    });
});

// Function to add block
function addBlock(type) {
    fetch('{{ route("blocks.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            page_id: {{ $activePage->id ?? 'null' }},
            type: type,
            content: { text: 'New ' + type + ' block' }
        })
    }).then(response => {
        if (response.ok) {
            location.reload();
        }
    });
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
    titleInput.focus();
    
    // Show modal
    modal.classList.remove('hidden');
}

// Show Add Page Modal
function showAddPageForm() {
    document.getElementById('addPageModal').classList.remove('hidden');
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
</script>
@endsection