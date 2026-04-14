# Xendit Integration - Quick Reference

## 🚀 Quick Start

### 1. Setup Environment (5 menit)
```bash
# Copy .env template jika belum ada
cp .env.example .env

# Add ini ke .env Anda
XENDIT_ENABLED=true
XENDIT_API_KEY=your_api_key_here
XENDIT_SECRET_KEY=your_secret_key_here
XENDIT_IS_PRODUCTION=false
XENDIT_DISBURSEMENT_API_KEY=your_disbursement_key_here
```

### 2. Get Xendit API Key (10 menit)
1. Go to https://dashboard.xendit.co
2. Sign up or login
3. Navigate to Settings → API Keys
4. Copy your API Key
5. Paste di `.env`

### 3. Clear Cache (1 menit)
```bash
php artisan config:clear
php artisan cache:clear
```

### 4. Test Payment (5 menit)
- Open browser: `http://localhost/checkout/[product-id]`
- Click payment button
- Try payment dengan virtual account test

---

## 📱 Payment Modal Usage

### Simple Usage in Blade
```blade
<!-- Open modal dengan button -->
<button onclick="openXenditModal()" class="btn btn-primary">
    Bayar Sekarang
</button>

<!-- Include modal component -->
@include('payment-modal', [
    'amount' => 150000,
    'orderId' => 'ORDER-123'
])
```

### Via JavaScript
```javascript
// Open modal
openXenditModal();

// Close modal  
closeXenditModal();

// Check payment status
xpmCheckStatus();

// Copy VA number
xpmCopy('xpm-va-number', buttonElement);
```

---

## 🔧 Service Usage Examples

### Create Invoice
```php
use App\Services\XenditPaymentService;

$service = app(XenditPaymentService::class);

$response = $service->createInvoice([
    'external_id' => 'ORDER-123',
    'description' => 'Product Purchase',
    'amount' => 150000,
    'customer_name' => 'John Doe',
    'customer_email' => 'john@example.com',
    'payment_methods' => ['VIRTUAL_ACCOUNT_BCA'],
]);

if ($response['success']) {
    $invoiceUrl = $response['invoice_url'];
    $invoiceId = $response['data']['id'];
} else {
    $error = $response['message'];
}
```

### Check Payment Status
```php
$result = $service->verifyPayment($invoiceId);

if ($result['success']) {
    echo $result['status'];  // e.g., "PAID"
    echo $result['amount'];  // e.g., 150000
}
```

### Handle Webhook
```php
use App\Http\Controllers\XenditWebhookController;

$controller = new XenditWebhookController();
$response = $controller->handleInvoiceCallback($request);
```

---

## 🧪 Testing URLs

### Development
- API Base: `https://api.xendit.co/v4` (production)
- Dashboard: https://dashboard.xendit.co

### Sandbox Testing
- Use sandbox API key
- Test credentials di Xendit dashboard

### Test Payment Methods
```
Virtual Account BCA:
  Number: 1234567890123
  Status: ACCEPTING_PAYMENTS

QRIS:
  QR Code oto-generate
  Status: ACCEPTING_PAYMENTS

E-Wallet:
  Redirect ke platform
```

---

## 📊 Database Schema

### Transaction Table
```
Order_ID         | Payment Method | Status    | Amount | Created_At
ORDER-123        | VIRTUAL_ACCOUNT_BCA | PAID | 150000 | 2024-01-01
ORDER-124        | QRIS          | PENDING   | 200000 | 2024-01-01
```

**Key Columns**:
- `order_id` - External ID di Xendit
- `transaction_id` - Invoice ID dari Xendit
- `status` - settlement, pending, failed, expired
- `midtrans_response` - JSON response dari Xendit

---

## 🔐 Security Details

### CSRF Protection
```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'webhook/xendit/*',    // Xendit webhooks
    'webhook/midtrans/*',  // Legacy
];
```

### Basic Auth
```php
// XenditPaymentService menggunakan:
Http::withBasicAuth($apiKey, '')
```

### Webhook Verification (Optional)
```php
// Check X-Callback-Token header
$token = $request->header('X-Callback-Token');
```

---

## 📝 Logging

### View Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log

# Search untuk Xendit logs
grep "Xendit" storage/logs/laravel.log
```

### Log Examples
```
[2024-01-01 10:30:45] local.INFO: Xendit Invoice Created: {
  "external_id": "ORDER-123",
  "invoice_id": "6123456789abcdef"
}

[2024-01-01 10:31:45] local.INFO: Xendit Webhook received: {
  "event": "invoice.paid",
  "external_id": "ORDER-123"
}
```

---

## 🎯 Common Tasks

### Create Test Invoice
```bash
php artisan tinker

# Create invoice
>>> $service = app('App\Services\XenditPaymentService')
>>> $response = $service->createInvoice([
    'external_id' => 'TEST-' . time(),
    'description' => 'Test Payment',
    'amount' => 50000,
    'customer_name' => 'Test User',
    'customer_email' => 'test@example.com',
    'payment_methods' => ['VIRTUAL_ACCOUNT_BCA']
])
>>> dd($response)
```

### Check Transaction Status
```bash
php artisan tinker

# Find transaction
>>> $tx = \App\Models\Transaction::where('order_id', 'ORDER-123')->first()
>>> $tx->status
>>> json_decode($tx->midtrans_response, true)
```

### Mark Payment as Settled (Manual)
```bash
php artisan tinker

>>> $tx = \App\Models\Transaction::find(1)
>>> $tx->status = 'settlement'
>>> $tx->save()
```

---

## ⚠️ Error Handling

### Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| `401 Unauthorized` | Invalid API Key | Verify key di .env |
| `422 Unprocessable Entity` | Invalid payload | Check required fields |
| `404 Not Found` | Invoice tidak ada | Check external_id valid |
| `Webhook not received` | URL salah/firewall | Verify URL di dashboard |

### Debug Mode
```php
// Temporarily enable debug logging
\Log::debug('Xendit Request', [
    'url' => $url,
    'payload' => $payload,
    'response' => $response,
]);
```

---

## 🚨 Production Checklist

- [ ] API Key di production
- [ ] XENDIT_IS_PRODUCTION=true di .env
- [ ] Webhook URL set di dashboard
- [ ] SSL/HTTPS enabled
- [ ] Logging monitored
- [ ] Error alerting configured
- [ ] Rate limiting set
- [ ] Database backup automated

---

## 📞 Quick Help

### Xendit Support
- Email: support@xendit.co
- Chat: Dashboard → Help
- Docs: https://xendit.co/docs

### Project Support
- Check logs: `storage/logs/laravel.log`
- Debug di tinker: `php artisan tinker`
- Test API: Postman/curl

---

## 💾 Useful Commands

```bash
# Config clear
php artisan config:clear && php artisan cache:clear

# Fresh install (risky)
php artisan fresh

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Start tinker
php artisan tinker

# Run queue (jika ada)
php artisan queue:work

# Serve locally
php artisan serve

# View logs
tail -f storage/logs/laravel.log
grep "pattern" storage/logs/laravel.log
```

---

## 📲 Payment Flow Diagram

```
User Interface
        ↓
payment-modal.blade.php (Xendit UI)
        ↓
POST /payment/create
        ↓
Api\PaymentController::create()
        ↓
XenditPaymentService::createInvoice()
        ↓
Xendit Dashboard
        ↓
Return invoice_url + details
        ↓
Modal menampilkan payment instructions
        ↓
Customer pays via bank/e-wallet
        ↓
Xendit callback → /webhook/xendit/invoice
        ↓
XenditWebhookController::handleInvoiceCallback()
        ↓
Update transaction status
        ↓
Process payment (create sales, credit seller, etc)
        ↓
Success page / notification
```

---

**Last Updated**: March 2024
**Version**: 1.0
**Status**: Production Ready

---
