# Fitur Penarikan Saldo Midtrans - Payou.id

Dokumentasi lengkap untuk implementasi fitur penarikan saldo dari wallet Midtrans ke rekening bank.

## 📋 Daftar Isi

1. [Persyaratan](#persyaratan)
2. [Instalasi](#instalasi)
3. [Konfigurasi](#konfigurasi)
4. [Penggunaan](#penggunaan)
5. [API Endpoints](#api-endpoints)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

## 🔧 Persyaratan

- PHP >= 8.1
- Laravel >= 10.x
- MySQL/PostgreSQL
- Akun Midtrans (Sandbox atau Production)
- Midtrans Iris API enabled

## 📦 Instalasi

### 1. Copy File-File yang Diperlukan

```bash
# Model
cp Withdrawal.php app/Models/

# Service
cp MidtransPayoutService.php app/Services/

# Controller
cp WithdrawalController.php app/Http/Controllers/

# Views
cp dashboard.blade.php resources/views/
cp withdrawals_index.blade.php resources/views/withdrawals/index.blade.php

# Config
cp midtrans.php config/
```

### 2. Jalankan Migration

Tabel `withdrawals` sudah ada di database Anda berdasarkan screenshot. Pastikan struktur sesuai:

```sql
CREATE TABLE `withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` bigint(20) NOT NULL,
  `status` enum('pending','approved','rejected','completed','cancelled') NOT NULL DEFAULT 'pending',
  `bank_name` varchar(255) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `payout_id` varchar(255) DEFAULT NULL,
  `midtrans_response` longtext DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdrawals_user_id_foreign` (`user_id`),
  KEY `withdrawals_approved_by_foreign` (`approved_by`),
  CONSTRAINT `withdrawals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `withdrawals_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Update User Model

Tambahkan relationship di `app/Models/User.php`:

```php
public function withdrawals()
{
    return $this->hasMany(Withdrawal::class);
}

// Jika belum ada field balance di users table
// Tambahkan kolom balance:
// ALTER TABLE users ADD COLUMN balance BIGINT(20) DEFAULT 0 AFTER email;
```

### 4. Tambahkan Routes

Tambahkan di `routes/web.php`:

```php
use App\Http\Controllers\WithdrawalController;

// User withdrawal routes
Route::middleware(['auth'])->prefix('withdrawal')->name('withdrawal.')->group(function () {
    Route::get('/', [WithdrawalController::class, 'index'])->name('index');
    Route::post('/', [WithdrawalController::class, 'store'])->name('store');
    Route::post('/{id}/cancel', [WithdrawalController::class, 'cancel'])->name('cancel');
    Route::get('/banks', [WithdrawalController::class, 'getBanks'])->name('banks');
});

// Admin withdrawal routes
Route::middleware(['auth', 'admin'])->prefix('admin/withdrawal')->name('admin.withdrawal.')->group(function () {
    Route::post('/{id}/process', [WithdrawalController::class, 'process'])->name('process');
    Route::get('/{id}/check-status', [WithdrawalController::class, 'checkStatus'])->name('check-status');
});
```

### 5. Register Service Provider

Tambahkan di `app/Providers/AppServiceProvider.php`:

```php
use App\Services\MidtransPayoutService;

public function register()
{
    $this->app->singleton(MidtransPayoutService::class, function ($app) {
        return new MidtransPayoutService();
    });
}
```

## ⚙️ Konfigurasi

### 1. Setup Midtrans Credentials

Edit file `.env`:

```env
# Midtrans Configuration
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxx
MIDTRANS_MERCHANT_ID=G123456789
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IRIS_API_KEY=IRIS-xxxxxxxxxxxxx
```

**Cara mendapatkan credentials:**

1. Login ke [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Pilih Environment (Sandbox/Production)
3. Menu Settings → Access Keys
4. Copy Server Key dan Client Key
5. Untuk Iris API, aktifkan di Menu Settings → Iris Settings

### 2. Konfigurasi Minimum Withdrawal

Edit di `WithdrawalController.php` (line 44):

```php
'amount' => 'required|numeric|min:50000|max:10000000',
```

Sesuaikan nilai `min` dan `max` sesuai kebutuhan.

## 📖 Penggunaan

### Untuk User

#### 1. Membuat Permintaan Penarikan

Dari dashboard:
1. Klik tombol "Tarik" di section Saldo & Transaksi
2. Isi form penarikan:
   - Jumlah (minimal Rp 50.000)
   - Pilih bank
   - Nomor rekening
   - Nama pemilik rekening
   - Catatan (opsional)
3. Klik "Ajukan Penarikan"
4. Penarikan akan **langsung diproses** ke Midtrans
5. Dana akan masuk ke rekening dalam 1-3 hari kerja

#### 2. Melihat Riwayat Penarikan

Akses: `/withdrawal` atau klik menu "Riwayat Penarikan"

#### 3. Membatalkan Penarikan

Hanya bisa untuk status "Approved" yang belum completed:
- Buka riwayat penarikan
- Klik icon X (cancel) pada penarikan yang ingin dibatalkan
- Saldo akan otomatis dikembalikan

#### 4. Cek Status Penarikan

Untuk melihat status terkini dari Midtrans:
- Buka detail penarikan
- Klik "Refresh Status"
- Sistem akan mengecek status terbaru dari Midtrans

## 🔌 API Endpoints

### User Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/withdrawal` | List riwayat penarikan user |
| POST | `/withdrawal` | Buat dan proses penarikan (langsung ke Midtrans) |
| POST | `/withdrawal/{id}/cancel` | Batalkan penarikan |
| GET | `/withdrawal/{id}/check-status` | Cek status penarikan dari Midtrans |
| GET | `/withdrawal/banks` | Daftar bank yang didukung |

### Request & Response Examples

#### Create Withdrawal Request (Direct Payout)

```json
POST /withdrawal

Request:
{
    "amount": 100000,
    "bank_name": "BCA",
    "account_number": "1234567890",
    "account_name": "John Doe",
    "notes": "Penarikan bulanan"
}

Success Response (200):
{
    "success": true,
    "message": "Penarikan berhasil diproses! Dana akan masuk ke rekening Anda dalam 1-3 hari kerja.",
    "data": {
        "withdrawal_id": 1,
        "reference_no": "IRIS-123456789",
        "amount": "Rp 100.000",
        "status": "approved"
    }
}

Error Response (400):
{
    "success": false,
    "message": "Saldo tidak mencukupi untuk melakukan penarikan"
}
```

#### Cancel Withdrawal

```json
POST /withdrawal/{id}/cancel

Success Response (200):
{
    "success": true,
    "message": "Penarikan berhasil dibatalkan dan saldo dikembalikan ke akun Anda"
}
```

#### Check Status

```json
GET /withdrawal/{id}/check-status

Success Response (200):
{
    "success": true,
    "data": {
        "status": "completed",
        "midtrans_status": "processed",
        "reference_no": "IRIS-123456789"
    }
}
```

## 🧪 Testing

### Testing di Sandbox

1. Gunakan kredensial Sandbox dari Midtrans
2. Set `MIDTRANS_IS_PRODUCTION=false`
3. Bank account number untuk testing: gunakan nomor valid format

### Test Cases

```bash
# Test 1: Withdrawal dengan saldo cukup
curl -X POST http://localhost/withdrawal \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 100000,
    "bank_name": "BCA",
    "account_number": "1234567890",
    "account_name": "Test User"
  }'

# Test 2: Withdrawal dengan saldo tidak cukup
# Expected: Error 400

# Test 3: Cancel pending withdrawal
curl -X POST http://localhost/withdrawal/1/cancel

# Test 4: Admin approve withdrawal
curl -X POST http://localhost/admin/withdrawal/1/process \
  -H "Content-Type: application/json" \
  -d '{"action": "approve"}'
```

## 🔍 Troubleshooting

### 1. Error "Saldo tidak mencukupi"

**Penyebab:** User balance kurang dari jumlah penarikan

**Solusi:**
- Pastikan kolom `balance` ada di tabel `users`
- Cek saldo user: `SELECT balance FROM users WHERE id = ?`
- Top up saldo terlebih dahulu

### 2. Error "Midtrans API Key Invalid"

**Penyebab:** Kredensial Midtrans salah atau tidak aktif

**Solusi:**
- Verifikasi kredensial di `.env`
- Pastikan menggunakan kredensial yang sesuai (Sandbox/Production)
- Cek apakah Iris API sudah aktif di dashboard

### 3. Payout Gagal

**Penyebab:** 
- Nomor rekening tidak valid
- Bank code salah
- Insufficient balance di Midtrans

**Solusi:**
- Validasi nomor rekening sebelum submit
- Cek mapping bank code di `MidtransPayoutService::getBankCode()`
- Top up balance Midtrans jika perlu

### 4. Status Tidak Terupdate

**Penyebab:** Webhook dari Midtrans belum diterima

**Solusi:**
- Implementasikan webhook handler
- Manual check status: `GET /admin/withdrawal/{id}/check-status`

### 5. Database Connection Error

**Penyebab:** Migration belum dijalankan atau struktur tidak sesuai

**Solusi:**
```bash
php artisan migrate:fresh --seed
# atau
php artisan migrate
```

## 📝 Catatan Penting

### Keamanan

1. **Validasi Input:** Semua input sudah divalidasi di controller
2. **Authorization:** Middleware auth melindungi semua endpoint
3. **Rate Limiting:** Implementasikan rate limiting untuk prevent abuse:

```php
Route::middleware(['auth', 'throttle:5,1'])->group(function () {
    Route::post('/withdrawal', [WithdrawalController::class, 'store']);
});
```

### Best Practices

1. **Logging:** Semua transaksi sudah di-log
2. **Transaction:** Menggunakan DB transaction untuk data consistency
3. **Error Handling:** Try-catch di semua method kritis
4. **Status Tracking:** Status withdrawal lengkap dengan timestamp

### Flow Penarikan

```
1. User Request Withdrawal
   ↓
2. System Validates Balance & Data
   ↓
3. Call Midtrans Payout API (Direct)
   ↓
4. Deduct User Balance → Status: APPROVED/COMPLETED
   ↓
5. Midtrans Process to Bank (1-3 hari kerja)
   ↓
6. Dana Masuk ke Rekening User

Note: 
- Tidak ada approval admin
- Langsung diproses via Midtrans API
- User bisa cancel jika masih dalam proses
```

### Bank Codes yang Didukung

Lihat method `getBankCode()` di `MidtransPayoutService.php` untuk daftar lengkap.

Bank utama:
- BCA
- BNI
- BRI
- Mandiri
- CIMB
- Permata
- BSI (Bank Syariah Indonesia)
- Dan lainnya...

## 📞 Support

Jika mengalami masalah:

1. Cek log Laravel: `storage/logs/laravel.log`
2. Cek dokumentasi Midtrans: https://docs.midtrans.com/en/iris-disbursement/overview
3. Contact Midtrans support untuk masalah API

## 📄 License

MIT License - Silakan digunakan dan dimodifikasi sesuai kebutuhan.

---

**Dibuat untuk Payou.id** 🚀
