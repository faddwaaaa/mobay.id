@extends('layouts.dashboard')

@section('title', 'Manajemen Produk')

@section('content')

<div class="page-header">
    <div>
        <h1>Manajemen Produk</h1>
        <p class="subtitle">Kelola semua produk digital kamu</p>
    </div>
    
</div>

<!-- ================= PRODUCT CARDS ================= -->
<div class="links-grid">
    @forelse($products as $product)
    <div class="link-card" data-product-id="{{ $product->id }}">
        <div class="link-card-header">
            <div class="link-icon shop" style="background: {{ $product->color ?? '#2563eb' }}">
                @if($product->image_url)
                <img src="{{ asset($product->image_url) }}" alt="{{ $product->title }}" class="product-image">
                @else
                <i class="fas fa-box"></i>
                @endif
            </div>
            <div class="product-status {{ $product->is_active ? 'active' : 'inactive' }}">
                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
            </div>
        </div>

        <h3>{{ $product->title }}</h3>
        <p class="link-description">
            {{ Str::limit($product->description ?? 'Tidak ada deskripsi', 80) }}
        </p>

        <div class="link-stats">
            <div class="stat">
                <i class="fas fa-eye"></i> {{ $product->views ?? 0 }} views
            </div>
            <div class="stat">
                <i class="fas fa-shopping-bag"></i> {{ $product->sold_count ?? 0 }} sold
            </div>
            <div class="stat">
                <i class="fas fa-star"></i> {{ $product->rating ?? '0.0' }}
            </div>
        </div>

        <div class="product-price">
            @if($product->discount)
                <span class="original-price">
                    Rp {{ number_format($product->price,0,',','.') }}
                </span>
                <span class="discount-price">
                    Rp {{ number_format($product->discount,0,',','.') }}
                </span>
                <span class="discount-badge">
                    -{{ number_format((($product->price - $product->discount) / $product->price) * 100, 0) }}%
                </span>
            @else
                <span class="normal-price">
                    Rp {{ number_format($product->price,0,',','.') }}
                </span>
            @endif
        </div>

        <div class="link-actions">
            <button class="btn-action edit" data-id="{{ $product->id }}">
                <i class="fas fa-pen"></i>
            </button>
            <button class="btn-action delete" data-id="{{ $product->id }}">
                <i class="fas fa-trash"></i>
            </button>
            <button class="btn-action copy" data-id="{{ $product->id }}" title="Copy Link">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-box-open"></i>
        </div>
        <h3>Belum ada produk</h3>
        <p>Tambahkan produk pertama kamu untuk mulai berjualan</p>
        <button class="btn-primary" id="addFirstProductBtn">
            <i class="fas fa-plus"></i> Tambah Produk
        </button>
    </div>
    @endforelse
</div>

<!-- ================= LIFETIME TABLE ================= -->
<div class="content-section" style="margin-top:32px">
    <div class="section-header">
        <h2>Statistik Produk</h2>
        <div class="time-filter">
            <select class="filter-select">
                <option>30 Hari Terakhir</option>
                <option>7 Hari Terakhir</option>
                <option>Hari Ini</option>
                <option>All Time</option>
            </select>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Views</th>
                    <th>Sold</th>
                    <th>Pendapatan</th>
                    <th>Conversion</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <div class="product-cell">
                            @if($product->image_url)
                            <img src="{{ asset($product->image_url) }}" alt="{{ $product->title }}" class="table-product-image">
                            @endif
                            <div>
                                <div class="product-title">{{ $product->title }}</div>
                                <div class="product-category">{{ $product->category ?? 'Uncategorized' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="stat-cell">
                            <i class="fas fa-eye"></i>
                            {{ $product->views ?? 0 }}
                        </div>
                    </td>
                    <td>
                        <div class="stat-cell">
                            <i class="fas fa-shopping-bag"></i>
                            {{ $product->sold_count ?? 0 }}
                        </div>
                    </td>
                    <td>
                        <div class="revenue">
                            Rp {{ number_format(($product->sold_count ?? 0) * ($product->discount ?? $product->price),0,',','.') }}
                        </div>
                    </td>
                    <td>
                        @php
                            $views = $product->views ?? 1;
                            $sold = $product->sold_count ?? 0;
                            $conversion = $views > 0 ? ($sold / $views * 100) : 0;
                        @endphp
                        <div class="conversion {{ $conversion >= 5 ? 'high' : ($conversion >= 2 ? 'medium' : 'low') }}">
                            {{ number_format($conversion, 1) }}%
                        </div>
                    </td>
                    <td>
                        <span class="status-badge {{ $product->is_active ? 'active' : 'inactive' }}">
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-icon edit" data-id="{{ $product->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon view" data-id="{{ $product->id }}" title="Preview">
                                <i class="fas fa-external-link-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ================= EDIT MODAL ================= -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Produk</h3>
            <button class="modal-close" id="closeModal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editProductForm">
                <input type="hidden" id="editProductId" name="id">
                
                <div class="form-group">
                    <label for="editTitle">Nama Produk</label>
                    <input type="text" id="editTitle" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="editDescription">Deskripsi</label>
                    <textarea id="editDescription" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editPrice">Harga Normal</label>
                        <div class="input-with-icon">
                            <i class="fas fa-tag"></i>
                            <input type="number" id="editPrice" name="price" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editDiscount">Harga Diskon (opsional)</label>
                        <div class="input-with-icon">
                            <i class="fas fa-percentage"></i>
                            <input type="number" id="editDiscount" name="discount" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="editCategory">Kategori</label>
                    <select id="editCategory" name="category" class="form-control">
                        <option value="">Pilih Kategori</option>
                        <option value="digital">Produk Digital</option>
                        <option value="ebook">E-book</option>
                        <option value="software">Software</option>
                        <option value="template">Template</option>
                        <option value="course">Kursus Online</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="editImage">URL Gambar Produk</label>
                    <div class="image-upload-preview">
                        <div class="image-preview" id="imagePreview">
                            <i class="fas fa-image"></i>
                            <span>Preview gambar akan muncul di sini</span>
                        </div>
                        <input type="text" id="editImage" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="editIsActive" name="is_active" value="1">
                        <span class="checkmark"></span>
                        Produk Aktif
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" id="cancelEdit">Batal</button>
            <button class="btn-primary" id="saveEdit">Simpan Perubahan</button>
        </div>
    </div>
</div>

<style>
/* ================= GLOBAL STYLES ================= */
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --secondary: #64748b;
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --background: #f8fafc;
    --card-bg: #ffffff;
    --border: #e2e8f0;
}

/* ================= GRID ================= */
.links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

/* ================= CARD ================= */
.link-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
}

.link-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: var(--primary);
}

.link-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(to bottom, var(--primary), var(--primary-dark));
    border-radius: 16px 0 0 16px;
}

.link-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.link-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    overflow: hidden;
}

.link-icon img.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
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

.link-card h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #1e293b;
}

.link-description {
    font-size: 14px;
    color: var(--secondary);
    margin-bottom: 16px;
    line-height: 1.5;
}

/* ================= STATS ================= */
.link-stats {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
    padding: 12px;
    background: var(--background);
    border-radius: 10px;
}

.stat {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #475569;
}

.stat i {
    color: var(--primary);
}

/* ================= PRICE ================= */
.product-price {
    margin-bottom: 16px;
}

.original-price {
    text-decoration: line-through;
    color: var(--secondary);
    font-size: 14px;
    margin-right: 8px;
}

.discount-price {
    color: var(--danger);
    font-size: 18px;
    font-weight: 700;
    margin-right: 8px;
}

.normal-price {
    color: var(--primary);
    font-size: 18px;
    font-weight: 700;
}

.discount-badge {
    background: var(--danger);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

/* ================= ACTION BUTTONS ================= */
.link-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    padding-top: 16px;
    border-top: 1px solid var(--border);
}

.btn-action {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    background: var(--background);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-action:hover {
    transform: translateY(-2px);
}

.btn-action.edit {
    color: var(--primary);
}

.btn-action.edit:hover {
    background: #dbeafe;
}

.btn-action.delete {
    color: var(--danger);
}

.btn-action.delete:hover {
    background: #fee2e2;
}

.btn-action.copy {
    color: var(--success);
}

.btn-action.copy:hover {
    background: #d1fae5;
}

/* ================= EMPTY STATE ================= */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: var(--card-bg);
    border-radius: 16px;
    border: 2px dashed var(--border);
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 32px;
}

.empty-state h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #1e293b;
}

.empty-state p {
    color: var(--secondary);
    margin-bottom: 20px;
}

/* ================= TABLE SECTION ================= */
.content-section {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    margin-top: 32px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.section-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
}

.filter-select {
    padding: 8px 16px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--card-bg);
    color: #1e293b;
    font-size: 14px;
    cursor: pointer;
}

/* ================= TABLE ================= */
.table-container {
    overflow-x: auto;
    border-radius: 12px;
    border: 1px solid var(--border);
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

table th {
    background: var(--background);
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #475569;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
}

table td {
    padding: 16px;
    border-bottom: 1px solid var(--border);
    color: #475569;
}

table tr:hover {
    background: var(--background);
}

.product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.table-product-image {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
}

.product-title {
    font-weight: 500;
    color: #1e293b;
}

.product-category {
    font-size: 12px;
    color: var(--secondary);
    margin-top: 2px;
}

.stat-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.revenue {
    font-weight: 600;
    color: var(--success);
}

.conversion {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    width: fit-content;
}

.conversion.high {
    background: #dcfce7;
    color: #166534;
}

.conversion.medium {
    background: #fef3c7;
    color: #92400e;
}

.conversion.low {
    background: #fee2e2;
    color: #991b1b;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
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

.table-actions {
    display: flex;
    gap: 8px;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: none;
    background: var(--background);
    color: var(--primary);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    background: #dbeafe;
    transform: translateY(-2px);
}

/* ================= MODAL ================= */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    backdrop-filter: blur(4px);
}

.modal {
    background: var(--card-bg);
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    animation: modalSlideIn 0.3s ease;
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

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid var(--border);
}

.modal-header h3 {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: var(--secondary);
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.modal-close:hover {
    background: var(--background);
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-secondary {
    padding: 10px 20px;
    border: 1px solid var(--border);
    background: var(--card-bg);
    color: #475569;
    border-radius: 10px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-secondary:hover {
    background: var(--background);
}

/* ================= FORM STYLES ================= */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #475569;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--card-bg);
    color: #1e293b;
    font-size: 14px;
    transition: border-color 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.input-with-icon {
    position: relative;
}

.input-with-icon i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--secondary);
}

.input-with-icon .form-control {
    padding-left: 40px;
}

/* ================= IMAGE PREVIEW ================= */
.image-upload-preview {
    margin-top: 8px;
}

.image-preview {
    width: 100%;
    height: 120px;
    border: 2px dashed var(--border);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    background: var(--background);
    color: var(--secondary);
}

.image-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

/* ================= CHECKBOX ================= */
.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 14px;
    color: #475569;
}

.checkbox-label input {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid var(--border);
    border-radius: 6px;
    margin-right: 12px;
    position: relative;
    transition: all 0.2s ease;
}

.checkbox-label input:checked + .checkmark {
    background: var(--primary);
    border-color: var(--primary);
}

.checkbox-label input:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    color: white;
    font-size: 12px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal elements
    const editModal = document.getElementById('editModal');
    const closeModal = document.getElementById('closeModal');
    const cancelEdit = document.getElementById('cancelEdit');
    const addProductBtn = document.getElementById('addProductBtn');
    const addFirstProductBtn = document.getElementById('addFirstProductBtn');
    const saveEditBtn = document.getElementById('saveEdit');
    
    // Sample product data (in real app, this would come from the server)
    const sampleProducts = {
        1: {
            id: 1,
            title: "E-book Digital Marketing",
            description: "Panduan lengkap digital marketing untuk pemula hingga mahir",
            price: 149000,
            discount: 99000,
            category: "ebook",
            image_url: "/images/products/ebook.jpg",
            is_active: true
        },
        2: {
            id: 2,
            title: "Template Website Premium",
            description: "Koleksi template website modern dan responsif",
            price: 299000,
            discount: null,
            category: "template",
            image_url: "/images/products/template.jpg",
            is_active: true
        }
    };

    // Open modal functions
    function openEditModal(productId) {
        const product = sampleProducts[productId] || {
            id: productId,
            title: `Product ${productId}`,
            description: "Deskripsi produk",
            price: 100000,
            discount: "",
            category: "",
            image_url: "",
            is_active: true
        };

        // Fill form with product data
        document.getElementById('editProductId').value = product.id;
        document.getElementById('editTitle').value = product.title;
        document.getElementById('editDescription').value = product.description || '';
        document.getElementById('editPrice').value = product.price;
        document.getElementById('editDiscount').value = product.discount || '';
        document.getElementById('editCategory').value = product.category || '';
        document.getElementById('editImage').value = product.image_url || '';
        document.getElementById('editIsActive').checked = product.is_active;

        // Update image preview
        updateImagePreview(product.image_url);

        editModal.style.display = 'flex';
    }

    // Update image preview
    function updateImagePreview(imageUrl) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        if (imageUrl) {
            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = 'Preview';
            img.onerror = function() {
                preview.innerHTML = '<i class="fas fa-image"></i><span>Gambar tidak dapat dimuat</span>';
            };
            preview.appendChild(img);
        } else {
            preview.innerHTML = '<i class="fas fa-image"></i><span>Preview gambar akan muncul di sini</span>';
        }
    }

    // Event listeners for edit buttons
    document.querySelectorAll('.btn-action.edit').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const productId = this.getAttribute('data-id');
            openEditModal(productId);
        });
    });

    // Event listeners for table edit buttons
    document.querySelectorAll('.table-actions .edit').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const productId = this.getAttribute('data-id');
            openEditModal(productId);
        });
    });

    // Close modal functions
    function closeEditModal() {
        editModal.style.display = 'none';
    }

    // Close modal when clicking overlay
    editModal.addEventListener('click', function(e) {
        if (e.target === editModal) {
            closeEditModal();
        }
    });

    // Close modal buttons
    closeModal.addEventListener('click', closeEditModal);
    cancelEdit.addEventListener('click', closeEditModal);

    // Add product buttons
    [addProductBtn, addFirstProductBtn].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function() {
                // Reset form for new product
                document.getElementById('editProductForm').reset();
                document.getElementById('editProductId').value = '';
                document.getElementById('editIsActive').checked = true;
                updateImagePreview('');
                
                // Change modal title
                document.querySelector('.modal-header h3').textContent = 'Tambah Produk Baru';
                
                editModal.style.display = 'flex';
            });
        }
    });

    // Save edit
    saveEditBtn.addEventListener('click', function() {
        const form = document.getElementById('editProductForm');
        const productId = document.getElementById('editProductId').value;
        
        // In real app, send AJAX request to server
        console.log('Saving product:', {
            id: productId,
            title: document.getElementById('editTitle').value,
            description: document.getElementById('editDescription').value,
            price: document.getElementById('editPrice').value,
            discount: document.getElementById('editDiscount').value,
            category: document.getElementById('editCategory').value,
            image_url: document.getElementById('editImage').value,
            is_active: document.getElementById('editIsActive').checked
        });

        // Show success message
        alert(productId ? 'Produk berhasil diperbarui!' : 'Produk berhasil ditambahkan!');
        
        // Close modal
        closeEditModal();
        
        // Reload page to see changes (in real app, update UI without reload)
        setTimeout(() => {
            location.reload();
        }, 1000);
    });

    // Delete product
    document.querySelectorAll('.btn-action.delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const productId = this.getAttribute('data-id');
            
            if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                // In real app, send AJAX DELETE request
                console.log('Deleting product:', productId);
                
                // Show success message
                alert('Produk berhasil dihapus!');
                
                // Reload page
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });
    });

    // Copy product link
    document.querySelectorAll('.btn-action.copy').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const productId = this.getAttribute('data-id');
            const productUrl = `${window.location.origin}/product/${productId}`;
            
            // Copy to clipboard
            navigator.clipboard.writeText(productUrl).then(() => {
                // Show tooltip or temporary change icon
                const icon = this.querySelector('i');
                const originalClass = icon.className;
                
                icon.className = 'fas fa-check';
                this.style.background = '#d1fae5';
                
                setTimeout(() => {
                    icon.className = originalClass;
                    this.style.background = '';
                }, 2000);
            });
        });
    });

    // Live image preview
    document.getElementById('editImage').addEventListener('input', function() {
        updateImagePreview(this.value);
    });

    // Add some interactivity to cards
    document.querySelectorAll('.link-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Only navigate if not clicking action buttons
            if (!e.target.closest('.btn-action')) {
                const productId = this.getAttribute('data-product-id');
                // Navigate to product detail or preview
                window.location.href = `/products/${productId}`;
            }
        });
    });
});
</script>
@endsection