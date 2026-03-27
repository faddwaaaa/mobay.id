# Storage Capacity Configuration & Quick Reference

## ⚙️ Configuration

### Storage Limits (bytes)
```php
// app/Services/StorageService.php

const FREE_STORAGE_LIMIT = 20971520;   // 20 MB
const PRO_STORAGE_LIMIT = 1073741824;  // 1 GB
```

**Untuk mengubah limits, edit konstanta di `StorageService::class`**

### Threshold Warnings
```php
// app/Services/StorageService.php

// Free user: warning jika > 80% full
if (!$user->isPro() && $usagePercentage > 80) { ... }

// Pro user: warning jika > 80% full
if ($user->isPro() && $usagePercentage > 80) { ... }
```

---

## 🎯 Quick Usage

### 1. Check User Storage
```php
$user = auth()->user();
$info = $user->getStorageInfo();

// $info = [
//     'used' => 1048576,                  // bytes
//     'used_formatted' => '1 MB',         // readable
//     'limit' => 20971520,                // bytes
//     'limit_formatted' => '20 MB',       // readable
//     'available' => 19922944,            // bytes
//     'available_formatted' => '19 MB',   // readable
//     'percentage' => 5.0,                // %
//     'plan' => 'Free',                   // subscription
// ]
```

### 2. Validate Upload
```php
$user = auth()->user();
$fileSize = $request->file('image')->getSize();

$validation = $user->canUpload($fileSize);

if (!$validation['can_upload']) {
    return back()->withErrors([
        'storage' => $validation['message']
    ]);
}

// Lanjutkan upload...
$path = $request->file('image')->store('...', 'public');
$user->addStorageUsage($fileSize);
```

### 3. Record Upload
```php
// Setelah file berhasil di-save ke storage
$user->addStorageUsage($file->getSize());
```

### 4. Record Deletion
```php
// Sebelum delete file dari storage
$fileSize = Storage::size('public/' . $file->path);
Storage::delete('public/' . $file->path);
$user->removeStorageUsage($fileSize);
```

### 5. In Blade Template
```blade
@php $storage = Auth::user()->getStorageInfo() @endphp

<div>
    <p>Kelanjutan: {{ $storage['used_formatted'] }} / {{ $storage['limit_formatted'] }}</p>
    <progress max="100" value="{{ $storage['percentage'] }}"></progress>
    <small>{{ round($storage['percentage'], 1) }}% used</small>
</div>
```

### 6. API Endpoint
```javascript
// Via JavaScript
fetch('/api/storage/info')
    .then(r => r.json())
    .then(data => {
        console.log(data.storage);
        // { used: 1MB, limit: 20MB, percentage: 5, ... }
    });
```

---

## 📋 Implementation Checklist for New Features

### Ketika menambah fitur upload file baru:

- [ ] Di `store()` method:
  - [ ] Hitung ukuran file sebelum upload
  - [ ] Panggil `$user->canUpload($fileSize)`
  - [ ] Jika error, return dengan error message
  - [ ] Upload file
  - [ ] Panggil `$user->addStorageUsage($file->getSize())`

- [ ] Di `destroy()` method:
  - [ ] Get file size sebelum delete
  - [ ] Delete file dari storage
  - [ ] Panggil `$user->removeStorageUsage($fileSize)`

- [ ] Di `update()` method:
  - [ ] Untuk file baru: check storage + addStorageUsage()
  - [ ] Untuk file dihapus: removeStorageUsage()

---

## 🔍 Debugging

### Check storage di Tinker
```bash
php artisan tinker

>>> $user = User::find(1);
>>> $user->getStorageInfo();
>>> $user->canUpload(1024000); // 1MB
```

### View Database
```bash
# MySQL / MariaDB
SELECT id, email, storage_used, storage_limit, subscription_plan 
FROM users;
```

### Force Update Storage Limit
```php
// Quick fix jika storage_limit salah
$user = User::find(1);
$user->updateStorageLimit();
```

### Reset Storage (Admin Only)
```php
// Reset storage_used untuk user tertentu
$user = User::find(1);
$user->update(['storage_used' => 0]);
```

---

## 🚨 Common Issues

### Issue: storage_limit tidak update saat upgrade

**Cause**: Observer tidak terdaftar

**Fix**:
```php
// app/Providers/AppServiceProvider.php
User::observe(UserObserver::class);
```

### Issue: File upload tapi storage_used tidak bertambah

**Cause**: Lupa panggil `addStorageUsage()`

**Fix**: Pastikan di setiap method upload ada:
```php
$user->addStorageUsage($file->getSize());
```

### Issue: uploadnya berhasil tapi error message ditampilkan

**Cause**: Error storage tapi upload tetap jalan

**Fix**: Pastikan ada `return` setelah validation error:
```php
$validation = $user->canUpload($fileSize);
if (!$validation['can_upload']) {
    return back()->with('error', $validation['message']);  // PENTING: return
}
```

---

## 📊 Monitoring Queries

### Users dengan storage penuh
```sql
SELECT * FROM users 
WHERE storage_used >= storage_limit 
ORDER BY storage_percentage DESC;
```

### Top users by storage usage
```sql
SELECT email, subscription_plan, storage_used, storage_limit,
       ROUND((storage_used/storage_limit)*100, 2) as percentage
FROM users 
WHERE storage_used > 0 
ORDER BY storage_used DESC 
LIMIT 10;
```

### Free users vs Pro users storage
```sql
SELECT subscription_plan, 
       COUNT(*) as user_count,
       SUM(storage_used) as total_used,
       SUM(storage_limit) as total_limit
FROM users 
GROUP BY subscription_plan;
```

---

## 📚 File Locations

```
laravel/
├── app/
│   ├── Services/StorageService.php           ← Core logic
│   ├── Models/User.php                       ← Model methods
│   ├── Observers/UserObserver.php            ← Auto updates
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ProductController.php         ← Product upload
│   │   │   ├── ProfileController.php         ← Avatar upload
│   │   │   └── DashboardController.php       ← API endpoint
│   │   └── Middleware/AddStorageToView.php   ← View sharing
│   └── Console/Commands/InitializeUserStorage.php
├── database/migrations/
│   └── 2026_03_26_000001_add_storage...php   ← Schema
├── resources/views/components/
│   └── storage-alert.blade.php               ← UI component
├── bootstrap/app.php                         ← Middleware reg
├── routes/web.php                            ← API route
└── STORAGE_CAPACITY_DOCUMENTATION.md         ← Full docs
```

---

## ✅ Validation Logic

```
User submit upload?
    ↓
Get file size(s)
    ↓
StorageService::validateUpload($user, $totalSize)
    ↓
    ├─ Hitung: available = limit - used
    ├─ Cek: totalSize > available?
    │   ├─ YES:
    │   │   ├─ Free user → ERROR (can_upload: false)
    │   │   └─ Pro user → WARNING (can_upload: false)
    │   └─ NO:
    │       ├─ Cek percentage > 80%?
    │       │   ├─ YES → WARNING (can_upload: true)
    │       │   └─ NO → SUCCESS (can_upload: true)
    ↓
Controller check: can_upload?
    ├─ false → return error, STOP
    └─ true → proceed with upload
        ↓
        Upload & Save file
        ↓
        increment storage_used
```

---

**Last Updated**: 26 Maret 2026
