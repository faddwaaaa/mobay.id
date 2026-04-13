# Xendit Integration Setup Guide

Panduan lengkap untuk melakukan switch dari Midtrans ke Xendit.

## 📋 Daftar Isi
1. [Konfigurasi Dasar](#konfigurasi-dasar)
2. [Environment Variables](#environment-variables)
3. [Alur Pembayaran](#alur-pembayaran)
4. [Testing](#testing)
5. [Troubleshooting](#troubleshooting)

---

## Konfigurasi Dasar

### 1. **Buat Akun Xendit**
- Kunjungi https://dashboard.xendit.co
- Daftar akun dan verifikasi
- Dapatkan API Key dari dashboard

### 2. **Set Environment Variables**

Edit file `.env` Anda:

```bash
# Xendit Configuration
XENDIT_ENABLED=true
XENDIT_API_KEY=your_xendit_api_key_here
XENDIT_SECRET_KEY=your_xendit_secret_key_here
XENDIT_IS_PRODUCTION=false        # Set to true di production
XENDIT_DISBURSEMENT_ENABLED=true
XENDIT_DISBURSEMENT_API_KEY=your_disbursement_api_key_here
```

### 3. **Verify Installation**

```bash
# Test config
php artisan tinker
>>> config('xendit')
# Seharusnya menampilkan semua config Xendit
```

---

## Environment Variables

| Variable | Deskripsi | Wajib |
|----------|-----------|-------|
| `XENDIT_ENABLED` | Aktifkan/nonaktifkan Xendit | ✅ |
| `XENDIT_API_KEY` | API Key dari dashboard Xendit | ✅ |
| `XENDIT_SECRET_KEY` | Secret Key untuk webhook verification | ❌ |
| `XENDIT_IS_PRODUCTION` | Mode production/sandbox | ✅ |
| `XENDIT_DISBURSEMENT_API_KEY` | API Key khusus untuk payout | ❌ |

---

## Alur Pembayaran

### 1. **Customer Melakukan Pembayaran**

```
Customer → Modal Pembayaran → Pilih Metode
    ↓
Kirim request ke /payment/create
    ↓
XenditPaymentService membuat invoice
    ↓
Xendit mengembalikan invoice URL + payment details
    ↓
Modal menampilkan instruksi pembayaran
```

### 2. **Xendit Webhook Callback**

```
Xendit → /webhook/xendit/invoice (webhook handler)
    ↓
Verify & process payment
    ↓
Update Transaction status
    ↓
XenditWebhookController → handleSuccessfulPayment()
    ↓
Create ProductSale, kirim email, credit seller
```

### 3. **Payment Methods yang Didukung**

#### Virtual Account (VA)
- BCA, BNI, BRI, Mandiri, Permata, CIMB, BSI, BJB, BTN, Nobu, Neo Commerce

#### QRIS
- Semua bank dan e-wallet yang support QRIS

#### E-Wallet
- DANA, OVO, ShopeePay, LinkAja, AstraPay, Jenius Pay

#### Minimarket / Retail
- Indomaret, Alfamart

---

## Testing

### 1. **Test Payment Creation**

```bash
# Terminal 1: Monitor logs
tail -f storage/logs/laravel.log

# Terminal 2: Test API
curl -X POST http://localhost/payment/create \
  -H "Content-Type: application/json" \
  -d '{
    "channel_code": "VIRTUAL_ACCOUNT_BCA",
    "amount": 50000,
    "order_id": "TEST-' $(date +%s) '",
    "name": "Test User",
    "email": "test@example.com"
  }'
```

### 2. **Test Webhook**

```bash
# Simulate Xendit webhook
curl -X POST http://localhost/webhook/xendit/invoice \
  -H "Content-Type: application/json" \
  -H "X-Callback-Token: test-token" \
  -d '{
    "event": "invoice.paid",
    "data": {
      "id": "test-invoice-id",
      "external_id": "TEST-' $(date +%s) '",
      "status": "PAID",
      "paid_amount": 50000,
      "paid_at": "'$(date -u +%Y-%m-%dT%H:%M:%SZ)'"
    }
  }'
```

### 3. **Manual Testing di Sandbox**

- Login ke https://dashboard.sandbox.xendit.co
- Buat invoice manual
- Lihat payment details di dashboard
- Trigger webhook manual dari dashboard

---

## Troubleshooting

### ❌ API Key Tidak Valid

**Error**: `401 Unauthorized`

**Solusi**:
```bash
# Verify API key
echo $XENDIT_API_KEY

# Generate baru dari dashboard Xendit
# Update .env
php artisan config:clear
php artisan cache:clear
```

### ❌ Webhook Tidak Diterima

**Cek**:
```bash
# Verify webhook URL di Xendit dashboard
# Seharusnya: https://yourdomain.com/webhook/xendit/invoice

# Check CSRF exception
grep -n "webhook/xendit" app/Http/Middleware/VerifyCsrfToken.php
```

**Solusi**:
```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'webhook/midtrans/*',
    'webhook/xendit/*',
    'api/callback/*',
];
php artisan config:clear
```

### ❌ Invoice Tidak Terbuat

**Debug**:
```bash
# Check Xendit service
php artisan tinker
>>> app('App\Services\XenditPaymentService')

# Test API call
>>> $service = app('App\Services\XenditPaymentService')
>>> $result = $service->createInvoice([
    'external_id' => 'TEST-' . time(),
    'amount' => 50000,
    'customer_name' => 'Test',
    'customer_email' => 'test@test.com',
])
>>> dd($result)
```

### ❌ Payment Status Tidak Update

**Check logs**:
```bash
grep "Xendit Webhook" storage/logs/laravel.log
grep "handlePaymentPaid" storage/logs/laravel.log
```

**Verify**:
```bash
php artisan tinker
>>> Transaction::latest()->first()
# Check status dan xendit_response columns
```

---

## File Structure

```
config/xendit.php                                    # Config utama
app/Services/XenditPaymentService.php               # Create invoice, check status
app/Services/XenditPayoutService.php               # Create payout/disbursement
app/Http/Controllers/XenditWebhookController.php   # Handle webhook
app/Http/Controllers/Api/PaymentController.php     # API endpoints
routes/webhook_routes.php                          # Webhook routes
app/Http/Middleware/VerifyCsrfToken.php           # CSRF exceptions
```

---

## Next Steps

### 1. **Set Webhook di Xendit Dashboard**

- Login ke https://dashboard.xendit.co
- Settings → Webhook Management
- URL: `https://yourdomain.com/webhook/xendit/invoice`
- Event: Semua invoice events

### 2. **Test di Production**

Setelah testing di sandbox, update `.env`:
```
XENDIT_IS_PRODUCTION=true
XENDIT_API_KEY=your_production_api_key
```

### 3. **Monitor & Alert**

Pastikan logging aktif:
```php
// config/logging.php sudah proper
```

---

## Reference

- **Xendit Docs**: https://xendit.co/docs
- **API Reference**: https://xendit.co/api-reference
- **Dashboard**: https://dashboard.xendit.co
- **Support**: support@xendit.co

---

## Security Best Practices

✅ **DO:**
- Store API keys di `.env` (never in code)
- Verify webhook signatures
- Use HTTPS di production
- Log semua transactions
- Set proper rate limits

❌ **DON'T:**
- Commit `.env` ke git
- Gunakan sandbox key di production
- Disable webhook verification
- Expose API key di frontend

---

Generated: {{ date }}
Last Updated: {{ date }}
