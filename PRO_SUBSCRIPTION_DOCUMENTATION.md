# Pro Subscription Fitur - Dokumentasi Lengkap

## 📋 Ringkasan Implementasi

Fitur Pro Subscription telah berhasil diimplementasikan dengan alur pembayaran QRIS dari Xendit yang terintegrasi penuh.

### 🎯 Alur Pembayaran Pro

```
User Pilih Paket (Bulanan/Tahunan)
    ↓
Klik Tombol → Modal QRIS Muncul (Fixed Position, Tidak Bisa Di-Scroll)
    ↓
Backend Buat Invoice di Xendit
    ↓
Tampilkan QRIS Code di Modal
    ↓
User Scan QRIS di E-wallet/Bank
    ↓
Pembayaran Diproses
    ↓
Webhook Xendit Kirim Notifikasi ke Backend
    ↓
Sistem Otomatis Update User Status Pro
    ↓
Pro Aktif 30 hari (Monthly) atau 365 hari (Yearly)
    ↓
Redirect ke Halaman Success
```

---

## 🏗️ Struktur Fitur

### 1. **Database Migration**
📁 `database/migrations/2026_04_02_000001_add_pro_subscription_to_users_table.php`

**Kolom Baru:**
- `pro_until` (timestamp) - Tanggal Pro berakhir
- `pro_type` (enum) - Tipe paket: 'monthly' atau 'yearly'
- `xendit_invoice_id` (string) - Invoice ID dari Xendit
- `xendit_external_id` (string) - External ID dari Xendit

### 2. **Model Updates**
📁 `app/Models/User.php`

**Method Baru:**
- `isPro()` - Cek apakah user Pro (dihitung real-time dari pro_until)
- `isProActive()` - Cek apakah Pro masih aktif (pro_until > now)
- `getProRemainingDays()` - Get sisa hari Pro user

### 3. **Controllers**
📁 `app/Http/Controllers/ProSubscriptionController.php`

**Methods:**
- `createInvoice(Request $request)` - POST /pro/create-invoice
  - Buat invoice QRIS di Xendit
  - Return QRIS code + jumlah + durasi
  
- `handleCallback(Request $request)` - Webhook handler (bukan di route)
  
- `checkStatus(Request $request)` - GET /pro/status
  - Return Pro status user
  
- `paymentSuccess()` - GET /pro/payment/success
  - Halaman setelah pembayaran sukses
  
- `paymentFailed()` - GET /pro/payment/failed
  - Halaman jika pembayaran gagal

### 4. **Services**
📁 `app/Services/ProSubscriptionService.php`

**Methods:**
- `createInvoice(User $user, string $packageType)` - Buat invoice di Xendit
- `activatePro(User $user, string $packageType)` - Aktivasi Pro user
- `handlePaymentSuccess(array $data)` - Handle callback pembayaran sukses
- `validateCallback(array $data)` - Validasi signature Xendit

### 5. **Routes**
📁 `routes/web.php`

**Pro Routes (Middleware Auth):**
```php
Route::prefix('pro')->name('pro.')->group(function () {
    Route::post('/create-invoice', [ProSubscriptionController::class, 'createInvoice'])->name('create-invoice');
    Route::get('/status', [ProSubscriptionController::class, 'checkStatus'])->name('status');
    Route::get('/payment/success', [ProSubscriptionController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failed', [ProSubscriptionController::class, 'paymentFailed'])->name('payment.failed');
});
```

**Webhook Routes:**
```php
Route::post('/webhook/xendit/invoice', [XenditWebhookController::class, 'handleInvoiceCallback'])
    ->name('webhook.xendit.invoice');
```

### 6. **Views**
📁 `resources/views/pro/`

- `qris-modal.blade.php` - Modal QRIS (Fixed Position, tidak bisa di-scroll)
- `payment-success.blade.php` - Halaman sukses
- `payment-failed.blade.php` - Halaman gagal

### 7. **Premium Page Update**
📁 `resources/views/premium/index.blade.php`

- Tombol "Pilih paket bulanan" → `onclick="showProQrisModal('monthly')"`
- Tombol "Pilih paket tahunan" → `onclick="showProQrisModal('yearly')"`
- Include modal QRIS di bawah

---

## ⚙️ Konfigurasi Environment

Pastikan `.env` memiliki:

```bash
XENDIT_ENABLED=true
XENDIT_API_KEY=your_xendit_api_key
XENDIT_SECRET_KEY=your_xendit_secret_key
XENDIT_IS_PRODUCTION=false  # Set true untuk production
```

### Xendit Keys
- Dapatkan dari: https://dashboard.xendit.co
- Copy API Key (untuk create invoice)
- Copy Secret Key (untuk verifikasi callback)

---

## 💳 Harga Paket

| Paket | Harga | Durasi |
|-------|-------|--------|
| Pro Bulanan | Rp 49.900 | 30 hari |
| Pro Tahunan | Rp 500.000 | 365 hari |

*Edit harga di `app/Services/ProSubscriptionService.php` di fungsi `createInvoice()`*

---

## 🧪 Testing Guide

### 1. **Test Create Invoice**

```bash
# Login dulu, kemudian akses:
GET http://localhost/premium

# Klik tombol "Pilih paket bulanan" atau "Pilih paket tahunan"
# Modal QRIS akan muncul di tengah screen
```

### 2. **Test Modal Behavior**
- Modal harus fixed di tengah screen
- Tidak bisa di-scroll
- Close button di top-right
- Overlay background semi-transparent
- ESC key bisa close modal
- Click overlay bisa close modal

### 3. **Test QRIS Payment (Dev Mode)**
- Xendit otomatis generate QRIS di dev mode
- Gunakan Xendit test Payment Link untuk simulate pembayaran
- Atau scan QRIS dengan XenPay test app

### 4. **Test Webhook (Production)**
- Set webhook URL di Xendit Dashboard
- URL: `YOUR_APP_URL/webhook/xendit/invoice`
- Test payment akan trigger webhook

### 5. **Verify Pro Status**

```bash
# Login, jalankan di console:
fetch('/pro/status')
    .then(r => r.json())
    .then(data => console.log(data))

# Output:
{
  "is_pro": true,
  "is_pro_active": true,
  "pro_until": "2026-05-02T...",
  "pro_type": "monthly",
  "remaining_days": 30
}
```

### 6. **Test Database**

```bash
# Check user Pro status:
php artisan tinker

$user = User::find(1);
$user->isPro();           // true/false
$user->isProActive();     // true/false jika pro_until > now
$user->pro_until;        // Carbon instance
$user->pro_type;         // 'monthly' atau 'yearly'

# Extend Pro manually:
$user->update([
    'pro_until' => now()->addDays(30),
    'pro_type' => 'monthly',
]);
```

---

## 🔄 Auto Renewal & Expiration

**Opsional - Untuk Implementasi Kedepannya:**
- Buat Artisan command untuk check expired Pro users
- Set user is_pro = false jika pro_until < now
- Tunda renewal reminder 7 hari sebelum expire

```bash
# Scheduled command (app/Console/Kernel.php):
$schedule->command('pro:check-expiration')->daily();
```

---

## 🚨 Status Fitur & Known Limitations

### ✅ Sudah Implemented
- [x] Modal QRIS fixed position (tidak bisa di-scroll)
- [x] Create invoice Xendit
- [x] Handle callback pembayaran sukses
- [x] Update user Pro status otomatis
- [x] Pro duration 30 hari (monthly) / 365 hari (yearly)
- [x] Payment success page
- [x] Payment failed page
- [x] Routes & Controllers lengkap
- [x] Database migration
- [x] CSRF exception untuk webhook

### ⚠️ Catatan Penting
1. **Testing Mode**: Set `XENDIT_IS_PRODUCTION=false` untuk testing
2. **Xendit Account**: Diperlukan account Xendit dengan API key
3. **Webhook URL**: Harus registered di Xendit Dashboard
4. **QRIS Display**: Otomatis generated oleh Xendit API

### 🔮 Future Enhancements
- [ ] Auto renewal subscription
- [ ] Expiration reminders
- [ ] Receipt email
- [ ] Invoice history
- [ ] Upgrade/downgrade mid-subscription
- [ ] Pro usage analytics

---

## 📞 Troubleshooting

### Problem: Modal QRIS tidak muncul
**Solution:**
```bash
# Cek browser console untuk error AJAX
# Pastikan CSRF token ada di meta tag:
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Problem: Invoice tidak created
**Solution:**
```bash
# Cek Xendit API key di .env
# Cek Xendit account has enough balance
# Cek server error log: storage/logs/laravel.log
```

### Problem: Callback tidak diterima
**Solution:**
```bash
# 1. Pastikan webhook URL terdaftar di Xendit Dashboard:
#    https://dashboard.xendit.co → Settings → Webhooks

# 2. Pastikan CSRF exception ada:
#    app/Http/Middleware/VerifyCsrfToken.php
#    'webhook/xendit/*'

# 3. Test webhook dari Xendit Dashboard (resend test)

# 4. Check logs:
php artisan tail
```

### Problem: User Pro status tidak update
**Solution:**
```bash
# Cek database:
php artisan tinker
User::where('id', 1)->first()->pro_until;

# Atau manual test:
$user = User::find(1);
$proService = new \App\Services\ProSubscriptionService();
$proService->handlePaymentSuccess([
    'external_id' => 'pro-1-monthly-test123',
    'status' => 'PAID'
]);
```

---

## 📝 File Structure Summary

```
laravel/
├── app/
│   ├── Http/Controllers/
│   │   ├── ProSubscriptionController.php (BARU)
│   │   └── XenditWebhookController.php (UPDATED)
│   ├── Services/
│   │   └── ProSubscriptionService.php (BARU)
│   └── Models/
│       └── User.php (UPDATED)
├── database/
│   └── migrations/
│       └── 2026_04_02_000001_add_pro_subscription_to_users_table.php (BARU)
├── resources/views/
│   ├── premium/
│   │   └── index.blade.php (UPDATED)
│   └── pro/ (BARU)
│       ├── qris-modal.blade.php (BARU)
│       ├── payment-success.blade.php (BARU)
│       └── payment-failed.blade.php (BARU)
├── routes/
│   ├── web.php (UPDATED)
│   └── webhook_routes.php (VERIFIED)
└── config/
    └── xendit.php (SUDAH ADA)
```

---

## 🎉 Summary

Fitur Pro Subscription **FULLY IMPLEMENTED** dan siap digunakan:

1. ✅ User klik paket → Modal QRIS muncul
2. ✅ QRIS code ditampilkan dengan jumlah & durasi
3. ✅ User scan & bayar QRIS
4. ✅ Webhook Xendit notifikasi pembayaran sukses
5. ✅ Sistem otomatis update Pro user
6. ✅ Pro aktif sesuai durasi paket (30/365 hari)
7. ✅ Success page + Failed page

**Deploy Notes:**
- Run migration: `php artisan migrate`
- Update .env dengan Xendit keys
- Register webhook URL di Xendit Dashboard
- Test dengan `XENDIT_IS_PRODUCTION=false`
- Set `XENDIT_IS_PRODUCTION=true` untuk production

---

*Last Updated: April 2, 2026*
*Status: PRODUCTION READY ✅*
