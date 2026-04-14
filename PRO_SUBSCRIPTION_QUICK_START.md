# 🚀 Pro Subscription - Quick Setup Guide

## 1️⃣ Setup Database

```bash
# Run migration untuk tambah kolom Pro subscription
php artisan migrate
```

**Kolom yang ditambah:**
- `pro_until` - Kapan Pro berakhir
- `pro_type` - 'monthly' atau 'yearly'
- `xendit_invoice_id` - Invoice ID dari Xendit
- `xendit_external_id` - External ID (untuk link pembayaran ke user)

---

## 2️⃣ Configure Xendit (.env)

Tambahkan/update di `.env`:

```bash
XENDIT_ENABLED=true
XENDIT_API_KEY=xnd_test_xxxxxxxxxxxx
XENDIT_SECRET_KEY=xnd_s_test_xxxxxxxxxxxx
XENDIT_IS_PRODUCTION=false
```

📌 **Dapatkan keys dari:** https://dashboard.xendit.co/settings/developers

---

## 3️⃣ Webhook Configuration

1. Buka **Xendit Dashboard** → Settings → Webhooks
2. Tambahkan endpoint: `https://your-app.com/webhook/xendit/invoice`
3. Select events: `invoice.paid`, `invoice.expired`
4. Simpan

✅ **CSRF Exception sudah ada** di `app/Http/Middleware/VerifyCsrfToken.php`

---

## 4️⃣ Test Local Dengan Xendit Test Mode

Pastikan `.env` punya:
```bash
XENDIT_IS_PRODUCTION=false
```

### Test Scenario:

**A. Test Create Invoice**
```
1. Login ke aplikasi
2. Buka halaman: /premium
3. Klik "Pilih paket bulanan"
4. Modal QRIS akan muncul
5. Check browser development tools console untuk melihat response
```

**B. Lihat Invoice yang Dibuat**
```bash
# Buka Xendit Dashboard → Invoices
# Lihat invoice terbaru yang dibuat
```

**C. Simulate Pembayaran**
```
# Dari Xendit Dashboard:
1. Buka invoice terbaru
2. Klik "Resend Webhook" atau gunakan Xendit test payment
3. Sistem akan otomatis process callback
```

---

## 5️⃣ Verify Pro Status User

```bash
# Login ke aplikasi, buka browser console:

fetch('/pro/status')
    .then(r => r.json())
    .then(d => console.log(d))

# Output - User yang sudah Pro:
{
  "is_pro": true,
  "is_pro_active": true,
  "pro_until": "2026-05-02T10:30:00",
  "pro_type": "monthly",
  "remaining_days": 30
}

# Output - User Free:
{
  "is_pro": false,
  "is_pro_active": false,
  "pro_until": null,
  "pro_type": null,
  "remaining_days": null
}
```

---

## 6️⃣ Manual Test pada Database

```bash
php artisan tinker

# Cek user status Pro:
$user = User::find(1);
$user->isPro();          // true/false
$user->isProActive();    // true/false

# Extend/activate Pro manually:
$user->update([
    'pro_until' => now()->addDays(30),
    'pro_type' => 'monthly',
]);

# Check Pro expiration:
$user->getProRemainingDays();  // jumlah hari sisa
```

---

## 7️⃣ Production Deployment

Sebelum production:

1. **Update .env:**
   ```bash
   XENDIT_IS_PRODUCTION=true
   XENDIT_API_KEY=xnd_prod_xxxxxxxxxxxx  (production key)
   XENDIT_SECRET_KEY=xnd_s_prod_xxxxxxxxxxxx
   ```

2. **Setup Webhook di Xendit Dashboard Production:**
   - Settings → Webhooks
   - Endpoint: `https://your-production-domain.com/webhook/xendit/invoice`

3. **Test dengan real payment:**
   - Beli paket dengan real QRIS/e-wallet
   - Verify user Pro status update

4. **Monitor logs:**
   ```bash
   # Real-time logs:
   php artisan tail
   
   # Check webhook logs:
   tail -f storage/logs/laravel.log | grep -i webhook
   ```

---

## 📊 Harga Paket (Edit di ProSubscriptionService.php)

```php
$prices = [
    'monthly' => 49900,   // Rp 49.900
    'yearly' => 500000,   // Rp 500.000
];
```

---

## 🎯 User Journey

```
1. User buka /premium
2. Lihat 2 tombol: "Pilih paket bulanan" & "Pilih paket tahunan"
3. Klik tombol → Modal QRIS fix di tengah screen
4. Scan QRIS di e-wallet/bank
5. Bayar
6. Xendit webhook notifikasi backend
7. User otomatis jadi Pro
8. Redirect ke halaman sukses
9. Pro akan berlaku 30 hari (bulanan) atau 365 hari (tahunan)
```

---

## ✅ Checklist Implementasi

- [x] Database migration (pro_until, pro_type, xendit fields)
- [x] User model methods (isPro, isProActive, getProRemainingDays)
- [x] ProSubscriptionController (create invoice, status check)
- [x] ProSubscriptionService (invoice creation, activation)
- [x] Routes (pro/create-invoice, pro/status, pro/payment/*)
- [x] Modal QRIS (fixed position, tidak scrollable)
- [x] Premium page integration
- [x] Webhook handler di XenditWebhookController
- [x] Payment success view
- [x] Payment failed view
- [x] CSRF exception configuration

---

## 🆘 Common Issues & Solutions

| Masalah | Solusi |
|---------|--------|
| Modal tidak muncul | Cek CSRF token di head: `<meta name="csrf-token" content="{{ csrf_token() }}">` |
| Invoice tidak dibuat | Check XENDIT_API_KEY di .env, test API key di Xendit Dashboard |
| Callback tidak diproses | Verifikasi webhook URL di Xendit Dashboard, check logs di storage/logs/ |
| User Pro tidak update | Run: `php artisan queue:work` (jika async) atau check webhook response |
| Modal bisa di-scroll | Modal sudah di-built dengan `position: fixed` & `max-height` |

---

## 📞 Support

- Xendit Docs: https://docs.xendit.co
- Error Logs: `storage/logs/laravel.log`
- Database: Check `users.pro_until` & `users.pro_type`

---

**Status: ✅ PRODUCTION READY**

Semua fitur sudah terimplementasi dan siap digunakan!
