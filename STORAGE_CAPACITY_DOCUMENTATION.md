# Dokumentasi: Sistem Pembatasan Kapasitas Penyimpanan (Storage Capacity System)

> **Tanggal**: 26 Maret 2026  
> **Status**: ✅ Implementasi Selesai  
> **Versi**: 1.0

---

## 📋 Ringkasan

Sistem pembatasan kapasitas penyimpanan telah diimplementasikan untuk membedakan fitur antara user **Free** dan **Pro**. Sistem ini mengecek kapasitas storage sebelum file diupload dan memberikan notifikasi jika storage penuh atau hampir penuh.

---

## 🎯 Kebutuhan Bisnis

### Kapasitas Penyimpanan
- **User Free**: 20 MB (20,971,520 bytes)
- **User Pro**: 1 GB (1,073,741,824 bytes)

### Validasi
- **User Free**: Blokir upload jika storage penuh → ERROR
- **User Pro**: Izinkan upload, tapi tampilkan warning jika storage penuh → WARNING

---

## 🏗️ Struktur Implementasi

### 1. Database (Migration)
**File**: `database/migrations/2026_03_26_000001_add_storage_capacity_to_users_table.php`

Menambahkan 2 kolom ke tabel `users`:
```sql
- storage_used (bigInteger) — Jumlah bytes yang sudah digunakan
- storage_limit (bigInteger) — Batas maksimal storage dalam bytes
```

**Initialize**: 
```bash
php artisan migrate
php artisan storage:initialize  # Initialize untuk existing users
```

---

### 2. Service Layer
**File**: `app/Services/StorageService.php`

Kelas utility untuk mengelola storage:

#### Konstanta
```php
const FREE_STORAGE_LIMIT = 20971520;   // 20 MB
const PRO_STORAGE_LIMIT = 1073741824;  // 1 GB
```

#### Method Utama

| Method | Fungsi |
|--------|--------|
| `getStorageLimit(User $user)` | Dapatkan limit storage user berdasarkan plan |
| `updateStorageLimit(User $user)` | Update limit storage saat subscription berubah |
| `getAvailableStorage(User $user)` | Hitung sisa storage yang tersedia |
| `getStoragePercentage(User $user)` | Hitung persentase penggunaan storage |
| `formatBytes(int $bytes)` | Format bytes ke KB, MB, GB |
| `validateUpload(User $user, int $fileSize)` | **VALIDASI UTAMA** → Check apakah bisa upload |
| `addStorageUsage(User $user, int $fileSize)` | Tambah storage usage setelah upload sukses |
| `removeStorageUsage(User $user, int $fileSize)` | Kurangi storage usage saat file dihapus |
| `getStorageInfo(User $user)` | Info storage lengkap dalam array |

---

### 3. User Model Methods
**File**: `app/Models/User.php`

Menambahkan method shortcut untuk storage management:

```php
$user->canUpload($fileSize)           // Validasi upload
$user->addStorageUsage($fileSize)     // Tambah usage
$user->removeStorageUsage($fileSize)  // Kurangi usage
$user->getAvailableStorage()          // Sisa storage
$user->getStoragePercentage()         // Persentase
$user->getStorageInfo()               // Info lengkap
$user->updateStorageLimit()           // Update limit
```

---

### 4. Observer Pattern
**File**: `app/Observers/UserObserver.php`

Otomatis handle storage events:
- **created**: Initialize storage saat user baru dibuat
- **updated**: Update storage limit saat subscription plan berubah

**Registrasi**: `app/Providers/AppServiceProvider.php`

---

### 5. Controllers (Implementasi)

#### ProductController
File: `app/Http/Controllers/ProductController.php`

**Method: `store()` (Tambah Produk)**
```
1. Validasi: Check kapasitas + ukuran file
2. Jika user free dan storage penuh → ERROR (return with error message)
3. Jika user pro dan storage penuh → WARNING (izinkan tapi tampilkan warning)
4. Create product
5. Upload images → addStorageUsage()
6. Upload files (digital) → addStorageUsage()
```

**Method: `update()` (Edit Produk)**
```
1. Validasi: Check kapasitas untuk file baru
2. Handle delete files → removeStorageUsage()
3. Handle add files → addStorageUsage()
```

**Method: `destroy()` (Hapus Produk)**
```
1. Hapus semua image files → removeStorageUsage()
2. Hapus semua digital files → removeStorageUsage()
3. Delete dari database
```

#### ProfileController
File: `app/Http/Controllers/ProfileController.php`

**Method: `update()` (Update Avatar)**
```
1. Validasi: Check kapasitas untuk avatar baru
2. Jika error → return dengan error message
3. Hapus avatar lama → removeStorageUsage()
4. Upload avatar baru → addStorageUsage()
```

#### DashboardController
File: `app/Http/Controllers/DashboardController.php`

**Method: `getStorageInfo()` (API Endpoint)**
```
GET /api/storage/info
Response: {
    "used": 1024000,
    "used_formatted": "1 MB",
    "limit": 20971520,
    "limit_formatted": "20 MB",
    "available": 19947520,
    "available_formatted": "19 MB",
    "percentage": 4.88,
    "plan": "Free"
}
```

---

### 6. Middleware
**File**: `app/Http/Middleware/AddStorageToView.php`

Menambahkan storage info ke semua view via shared view data:
```php
$userStorageInfo       // Array lengkap storage info
$userStoragePercentage // Persentase penggunaan
```

**Registrasi**: `bootstrap/app.php`

---

### 7. View Components

#### Storage Alert Component
**File**: `resources/views/components/storage-alert.blade.php`

Dynamic alert untuk menampilkan status storage:

**Usage di blade template:**
```blade
@include('components.storage-alert', [
    'showStorageAlert' => true,
    'storageStatus' => 'warning',  // 'error', 'warning', 'info'
    'storageMessage' => 'Penyimpanan Anda hampir penuh...',
    'storagePercentage' => 85,
    'storageUsed' => '17 MB',
    'storageLimit' => '20 MB',
])
```

---

### 8. Console Command
**File**: `app/Console/Commands/InitializeUserStorage.php`

Initialize storage untuk existing users:
```bash
php artisan storage:initialize
```

Output:
```
✓ User: email@domain.com - Plan: pro - Limit: 1 GB
✓ Initialized storage for N users
```

---

## 🔄 Alur Kerja (Flow)

### 1️⃣ User Upload File (Produk, Avatar, dll)

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User submit upload file                                  │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Controller validasi: GET file size                       │
│    - Hitung total ukuran semua file                         │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. StorageService::validateUpload()                         │
│    - Hitung available storage                               │
│    - Cek apakah fileSize > available storage                │
└────────────────────────┬────────────────────────────────────┘
                         ↓
           ┌─────────────┴──────────────┐
           ↓                            ↓
    ┌────────────────┐         ┌────────────────┐
    │ Cukup Storage  │         │ Tidak Cukup    │
    └────────┬───────┘         │ Storage        │
             ↓                 └────────┬───────┘
    ├─ Pro user → WARNING               │
    ├─ Free user → SUCCESS              │ ─→ ERROR MESSAGE
    ↓                                    ↓
 UPLOAD FILE                     RETURN ERROR
 & SAVE                          (Block Upload)
 ↓
 StorageService::addStorageUsage()
 (Increment storage_used)
```

### 2️⃣ User Hapus File

```
User delete file
        ↓
Get file size
        ↓
Delete file dari storage
        ↓
StorageService::removeStorageUsage()
(Decrement storage_used)
```

### 3️⃣ User Upgrade ke Pro

```
User upgrade subscription
        ↓
Update subscription_plan = 'pro'
        ↓
UserObserver::updated() triggered
        ↓
StorageService::updateStorageLimit($user)
        ↓
storage_limit updated: 20MB → 1GB
```

---

## 📊 Respons Validasi Storage

### Format Response
```php
[
    'can_upload' => bool,           // Boleh upload atau tidak
    'status' => 'success|warning|error',
    'message' => string,            // Pesan untuk user
    'available' => int,             // Bytes tersisa
    'usage_percentage' => float,    // Persentase penggunaan
]
```

### Scenario 1: Free User, Storage Penuh
```php
[
    'can_upload' => false,
    'status' => 'error',
    'message' => 'Penyimpanan Anda penuh! Upgrade ke Pro...',
    'available' => 0,
    'usage_percentage' => 100.0,
]
// → BLOCK UPLOAD, Show error message
```

### Scenario 2: Free User, Storage Hampir Penuh (>80%)
```php
[
    'can_upload' => true,
    'status' => 'warning',
    'message' => '⚠️ Penyimpanan Anda hampir penuh (85%)...',
    'available' => 3145728,
    'usage_percentage' => 85.0,
]
// → ALLOW UPLOAD, Show warning message
```

### Scenario 3: Pro User, Cukup Storage
```php
[
    'can_upload' => true,
    'status' => 'success',
    'message' => 'Upload berhasil. Sisa penyimpanan: 900 MB',
    'available' => 943718400,
    'usage_percentage' => 12.0,
]
// → ALLOW UPLOAD, Success
```

---

## 🔧 Penggunaan di Controller

### Contoh 1: Validasi Upload di ProductController
```php
$user = Auth::user();
$filesToUpload = $request->file('images');

// Hitung total ukuran
$totalSize = 0;
foreach ($filesToUpload as $file) {
    $totalSize += $file->getSize();
}

// Validasi
$validation = $user->canUpload($totalSize);
if (!$validation['can_upload']) {
    return back()->with('storage_error', $validation['message']);
}

// Upload files
foreach ($filesToUpload as $file) {
    $path = $file->store('products/images', 'public');
    $product->images()->create(['image' => $path]);
    
    // Tambah storage usage SETELAH file sukses diupload
    $user->addStorageUsage($file->getSize());
}
```

### Contoh 2: Menghapus File
```php
$file = ProductImage::find($imageId);
if (Storage::exists('public/' . $file->image)) {
    $fileSize = Storage::size('public/' . $file->image);
    Storage::delete('public/' . $file->image);
    
    // Kurangi storage usage saat file dihapus
    $user->removeStorageUsage($fileSize);
}
$file->delete();
```

### Contoh 3: Get Storage Info di View
```blade
@php
    $storage = Auth::user()->getStorageInfo();
@endphp

<div>
    <p>Penggunaan Storage: {{ $storage['percentage'] }}%</p>
    <p>{{ $storage['used_formatted'] }} / {{ $storage['limit_formatted'] }}</p>
    <progress value="{{ $storage['percentage'] }}" max="100"></progress>
</div>
```

---

## 📌 API Endpoint

### Get Storage Info
```
GET /api/storage/info
Headers: 
    Authorization: Bearer {token}
    Accept: application/json

Response:
{
    "success": true,
    "storage": {
        "used": 1048576,
        "used_formatted": "1 MB",
        "limit": 20971520,
        "limit_formatted": "20 MB",
        "available": 19922944,
        "available_formatted": "19 MB",
        "percentage": 5.0,
        "plan": "Free"
    }
}
```

---

## 🚀 Setup & Deployment

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Initialize Existing Users
```bash
php artisan storage:initialize
```

### 3. Cache Clear (kalau ada perubahan)
```bash
php artisan cache:clear
php artisan config:clear
```

---

## 📝 Checklist Implementasi

- ✅ Migration untuk storage_used & storage_limit
- ✅ StorageService class dengan method lengkap
- ✅ User model methods shortcut
- ✅ Observer untuk auto-update storage limit
- ✅ ProductController: validasi upload + add/remove storage
- ✅ ProfileController: avatar upload dengan storage check
- ✅ DashboardController: API endpoint storage info
- ✅ Middleware: AddStorageToView
- ✅ View component: storage-alert
- ✅ Console command: storage:initialize
- ✅ Route: API endpoint /api/storage/info
- ✅ Bootstrap: Register observer & middleware

---

## ⚠️ Edge Cases & Handling

| Case | Handling |
|------|----------|
| User upgrade Free → Pro | Observer update storage_limit 20MB → 1GB |
| User downgrade Pro → Free | Observer update storage_limit 1GB → 20MB |
| File delete saat sedang upload | removeStorageUsage mencegah negative value |
| Multiple file upload | Validasi total size sebelum mulai upload |
| Storage exactly full | Return can_upload: false |
| Pro user 100% full | Allow upload but show warning |
| User delete account | Optional: cleanup files via deleting event |

---

## 🔐 Security Notes

1. **File Size Validation**: Setiap file di-validate ukurannya sebelum storage check
2. **User Authorization**: Semua method check user ownership before modification
3. **Race Condition**: Storage used di-increment SETELAH file sukses disimpan (atomic)
4. **API Auth**: GET /api/storage/info memerlukan middleware 'auth'

---

## 📈 Monitoring & Maintenance

### Check Storage untuk User
```php
// Via Tinker
php artisan tinker
>>> $user = User::find(1);
>>> $user->getStorageInfo();

// Via Database
SELECT id, email, storage_used, storage_limit, subscription_plan
FROM users;
```

### Reset Storage (Manual)
```php
$user->update([
    'storage_used' => 0,
    'storage_limit' => \App\Services\StorageService::getStorageLimit($user)
]);
```

---

## 🎓 Testing

### Manual Test Scenario

**Test 1: Free User Upload**
1. Login sebagai free user
2. Upload file (misal produk image, harus < 20MB)
3. Check: File terupload, storage_used bertambah

**Test 2: Free User Storage Penuh**
1. Manually set user storage_used = 20971419 (hampir penuh)
2. Coba upload file 200 bytes
3. Check: Ditampilkan error message, upload diblokir

**Test 3: Upgrade Pro**
1. User dengan storage full (free user)
2. Coba upgrade ke pro (set subscription_plan = 'pro' manually)
3. Check: storage_limit should update to 1GB automatically
4. Coba upload file → should success

---

## 📚 Referensi File

| File | Fungsi |
|------|--------|
| `database/migrations/2026_03_26_000001_*.php` | Database schema |
| `app/Services/StorageService.php` | Storage logic |
| `app/Models/User.php` | Model methods |
| `app/Observers/UserObserver.php` | Event handling |
| `app/Http/Controllers/ProductController.php` | Product upload |
| `app/Http/Controllers/ProfileController.php` | Profile upload |
| `app/Http/Controllers/DashboardController.php` | API endpoint |
| `app/Http/Middleware/AddStorageToView.php` | View sharing |
| `app/Console/Commands/InitializeUserStorage.php` | Seeder |
| `resources/views/components/storage-alert.blade.php` | UI component |
| `bootstrap/app.php` | Middleware registration |
| `routes/web.php` | API route |

---

**Update Terakhir**: 26 Maret 2026  
**Status**: Deploy Ready ✅
