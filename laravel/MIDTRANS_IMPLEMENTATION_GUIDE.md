# MIDTRANS PAYMENT INTEGRATION - IMPLEMENTATION GUIDE

## 🚀 QUICK START

### 1. Run Migrations
```bash
php artisan migrate
```

Ini akan membuat 3 table baru:
- `transactions` - Menyimpan riwayat top-up dari Midtrans
- `withdrawals` - Menyimpan permintaan penarikan
- Update `users` table dengan kolom `balance`

### 2. Setup Environment Variables
Copy konfigurasi dari `.env.example` ke `.env`:

```env
MIDTRANS_ENABLED=true
MIDTRANS_MERCHANT_ID=G123456
MIDTRANS_CLIENT_KEY=VT-client-xxxxxxxxxxxxxxxx
MIDTRANS_SERVER_KEY=VT-server-xxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false

MIDTRANS_CALLBACK_URL=http://localhost:8000/api/callback/midtrans
MIDTRANS_FINISH_URL=http://localhost:8000/dashboard/topup/success
MIDTRANS_ERROR_URL=http://localhost:8000/dashboard/topup/error
MIDTRANS_UNFINISH_URL=http://localhost:8000/dashboard/topup/pending
```

**PENTING:** Ganti dengan kunci Midtrans Anda sendiri dari dashboard Midtrans.

### 3. Install Midtrans PHP SDK (Optional)
Jika ingin menggunakan SDK resmi Midtrans:
```bash
composer require midtrans/midtrans-php
```

Tapi implementasi di sini menggunakan HTTP requests saja (lebih simple).

---

## 📁 FILE STRUKTUR

Berikut adalah semua file yang telah dibuat:

### Database
```
database/migrations/
├── 2026_01_30_000000_add_balance_to_users_table.php
├── 2026_01_30_000001_create_transactions_table.php
└── 2026_01_30_000002_create_withdrawals_table.php
```

### Models
```
app/Models/
├── Transaction.php      (NEW)
├── Withdrawal.php       (NEW)
└── User.php             (UPDATED - add relationships)
```

### Services
```
app/Services/
├── MidtransService.php  (NEW)
└── PaymentService.php   (NEW)
```

### Controllers
```
app/Http/Controllers/
├── DashboardController.php      (UPDATED - add payment data)
├── TransactionController.php    (NEW)
└── CallbackController.php       (NEW)
```

### Requests
```
app/Http/Requests/
├── TopUpRequest.php     (NEW)
└── WithdrawRequest.php  (NEW)
```

### Config
```
config/midtrans.php             (NEW)
```

### Routes
```
routes/web.php                  (UPDATED - add payment routes)
```

---

## 💼 ARCHITECTURE

### Layer Structure
```
┌─────────────────┐
│    Frontend     │  (Blade templates / Vue)
├─────────────────┤
│  Controllers    │  (Orchestration only)
├─────────────────┤
│    Services     │  (Business logic)
├─────────────────┤
│     Models      │  (Data access)
├─────────────────┤
│    Database     │  (Persistence)
└─────────────────┘
```

### Service Layer
- **MidtransService**: Menangani semua integrasi dengan API Midtrans
- **PaymentService**: Menangani logika bisnis pembayaran (validasi, database transactions, etc)

### Controller Layer
- **DashboardController**: Menampilkan dashboard dengan balance
- **TransactionController**: Handle top-up dan withdraw requests
- **CallbackController**: Handle webhook dari Midtrans (CRITICAL - verify signature!)

---

## 🔄 TOP-UP FLOW

### User Perspective
```
1. User click "Top Up" → /dashboard/topup
   ↓
2. User input amount → Form validation (TopUpRequest)
   ↓
3. User click "Lanjutkan Pembayaran"
   ↓
4. POST /api/topup (create transaction)
   ↓
5. Redirect ke Midtrans Snap payment page
   ↓
6. User memilih payment method (CC, GCash, OVO, etc)
   ↓
7. User bayar
   ↓
8. Midtrans POST ke /api/callback/midtrans (verify signature!)
   ↓
9. Balance updated di database
   ↓
10. User redirect ke success page
```

### System Flow
```
User Input
    ↓
TopUpRequest (validation)
    ↓
TransactionController::createTopUp()
    ↓
PaymentService::createTopUp()
    ↓
MidtransService::createSnapTransaction()
    ↓
Save transaction (status: pending)
    ↓
Return snap token + redirect URL
    ↓
[User pays on Midtrans]
    ↓
CallbackController::handleMidtransCallback()
    ↓
Verify signature (CRITICAL!)
    ↓
PaymentService::handleSuccessfulPayment()
    ↓
Update transaction status → settlement
    ↓
Increment user balance
    ↓
Log transaction
```

---

## 🔐 SECURITY CHECKLIST

### ✅ SIGNATURE VERIFICATION
```php
// ALWAYS verify signature in CallbackController
$isValid = $this->midtransService->verifyCallbackSignature(
    $orderId,
    $transactionStatus,
    $grossAmount,
    $signatureKey
);

if (!$isValid) {
    return response('Unauthorized', 401);
}
```

**WHY?** Prevents attackers from:
- Modifying transaction status
- Changing amount
- Updating balance directly

### ✅ DATABASE TRANSACTIONS
```php
return DB::transaction(function () {
    // Update transaction status
    // Increment balance
    // All-or-nothing operation
});
```

**WHY?** Ensures data consistency. If any step fails, everything rolls back.

### ✅ AUTHORIZATION CHECKS
```php
public function authorize(): bool {
    return $this->user() !== null;  // Only authenticated users
}
```

### ✅ AMOUNT VALIDATION
```php
if ($amount < $minAmount || $amount > $maxAmount) {
    throw new Exception("Invalid amount");
}
```

### ✅ RATE LIMITING (TODO)
Add rate limiting to prevent abuse:
```php
Route::post('/api/topup', [TransactionController::class, 'createTopUp'])
    ->middleware('throttle:5,1');  // 5 requests per minute
```

### ✅ HTTPS ONLY (Production)
```php
// In CallbackController
if (!$request->isSecure()) {
    return response('HTTPS required', 403);
}
```

---

## 🧪 TESTING

### Test Top-Up Flow (Sandbox)
1. Go to `/dashboard/topup`
2. Enter amount: `20000` (Rp 20.000)
3. Click "Lanjutkan Pembayaran"
4. You'll redirect to Midtrans Snap
5. Use test card: `4811111111111114`
6. Click complete
7. Check database for updated transaction status
8. Check user balance incremented

### Test Withdrawal Flow
1. Go to `/dashboard/withdraw`
2. Enter amount: `50000` (Rp 50.000)
3. Click "Ajukan Penarikan"
4. Check `withdrawals` table for new record (status: pending)
5. Admin approves (separate admin panel needed)
6. Balance deducted

### Check Database
```bash
# View transactions
SELECT * FROM transactions;

# View withdrawals
SELECT * FROM withdrawals;

# Check user balance
SELECT id, name, balance FROM users;

# View failed transactions
SELECT * FROM transactions WHERE status IN ('failed', 'expired');
```

---

## 📊 API ENDPOINTS

### Public Endpoints
```
POST   /api/callback/midtrans      - Midtrans webhook (no auth needed)
```

### Authenticated Endpoints
```
GET    /dashboard/topup            - Show top-up form
GET    /dashboard/topup/success    - Show success page
GET    /dashboard/topup/error      - Show error page
GET    /dashboard/topup/pending    - Show pending page

GET    /dashboard/withdraw         - Show withdraw form

POST   /api/topup                  - Create top-up transaction
GET    /api/transactions           - Get transaction history
POST   /api/withdraw               - Create withdrawal request
GET    /api/withdrawals            - Get withdrawal history
GET    /api/dashboard/stats        - Get dashboard stats
```

---

## 🔧 CONFIGURATION

### Top-Up Settings
```php
// config/midtrans.php
'topup' => [
    'min_amount' => 10000,      // Rp 10.000
    'max_amount' => 10000000,   // Rp 10.000.000
],
```

### Withdrawal Settings
```php
'withdrawal' => [
    'min_amount' => 50000,      // Rp 50.000
    'max_amount' => 50000000,   // Rp 50.000.000
],
```

### Payment Methods
```php
'payment_methods' => [
    'credit_card' => true,
    'bank_transfer' => true,
    'e_wallet' => true,      // GCash, OVO, DANA, etc
    'qris' => true,
],
```

---

## 📝 LOGGING

All payment events are logged to `storage/logs/laravel.log`:

```log
[2026-01-30 10:15:23] local.INFO: Top-up transaction created {"user_id":1,"order_id":"PAYOU-1706594400-a1b2c3d4","amount":20000}

[2026-01-30 10:16:45] local.INFO: Payment processed successfully {"user_id":1,"order_id":"PAYOU-1706594400-a1b2c3d4","amount":20000,"new_balance":220000}

[2026-01-30 10:17:12] local.WARNING: Invalid callback signature {"order_id":"PAYOU-xxx","ip_address":"192.168.1.100"}
```

View logs:
```bash
tail -f storage/logs/laravel.log
```

---

## 🐛 TROUBLESHOOTING

### Issue: "Midtrans configuration is incomplete"
**Solution:** Check `.env` file has:
```
MIDTRANS_MERCHANT_ID=xxxxx
MIDTRANS_CLIENT_KEY=xxxxx
MIDTRANS_SERVER_KEY=xxxxx
```

### Issue: "Failed to create Midtrans transaction"
**Solution:** 
1. Check server key is correct
2. Check Midtrans is in sandbox/production mode
3. Check merchant_id exists
4. Check amount is within limits

### Issue: Callback not received
**Solution:**
1. Make sure callback URL is public (no auth middleware)
2. Check firewall allows POST requests
3. Check logs in Midtrans dashboard
4. Add logging: `Log::info('Callback received', $data);`

### Issue: Balance not updated after payment
**Solution:**
1. Check transaction status in database
2. Check callback signature verification passed
3. Check logs for errors
4. Verify database transaction didn't rollback

### Issue: Cannot verify signature
**Solution:**
```php
// Debug signature verification
$data = $request->all();
Log::info('Callback data', [
    'order_id' => $data['order_id'],
    'status' => $data['transaction_status'],
    'gross_amount' => $data['gross_amount'],
    'signature_key' => $data['signature_key'],
]);
```

---

## 🚀 PRODUCTION DEPLOYMENT

Before going live:

1. **Switch to Production**
   ```env
   MIDTRANS_IS_PRODUCTION=true
   MIDTRANS_MERCHANT_ID=your_production_merchant_id
   MIDTRANS_SERVER_KEY=your_production_server_key
   MIDTRANS_CLIENT_KEY=your_production_client_key
   ```

2. **Update Callback URL in Midtrans Dashboard**
   ```
   https://payou.id/api/callback/midtrans
   ```

3. **Enable HTTPS**
   ```php
   // In CallbackController or middleware
   if (!request()->isSecure()) {
       abort(403);
   }
   ```

4. **Add Rate Limiting**
   ```php
   Route::post('/api/topup')->middleware('throttle:5,1');
   ```

5. **Monitor Transactions**
   - Check Midtrans dashboard regularly
   - Monitor logs: `tail -f storage/logs/laravel.log`
   - Setup alerts for failed transactions

6. **Backup Database**
   - Regular backups before production
   - Test restore procedure

7. **Test Full Flow Again**
   - Do complete top-up transaction
   - Verify balance updated
   - Check withdrawal flow works

---

## 📚 REFERENCE LINKS

- Midtrans Documentation: https://docs.midtrans.com
- Midtrans API Reference: https://api-docs.midtrans.com
- Snap Integration Guide: https://docs.midtrans.com/en/snap/overview
- Callback Documentation: https://docs.midtrans.com/en/after-payment/http-notification

---

## ❓ FAQ

**Q: Can I use different payment methods?**
A: Yes! Configure in `config/midtrans.php` payment_methods. Midtrans supports 20+ methods.

**Q: What if user doesn't complete payment?**
A: Transaction stays in 'pending' status. After timeout, Midtrans sets it to 'expired'.

**Q: Can user cancel transaction?**
A: Yes, they can close Snap payment page. Transaction status becomes 'cancel'.

**Q: How to handle refunds?**
A: Not implemented in this version. Needs additional endpoint for refund requests.

**Q: What about webhook retries?**
A: Midtrans retries if callback doesn't return HTTP 200. Always return 200 in CallbackController.

**Q: Can I test with real money in sandbox?**
A: No, sandbox uses test cards. Real money only works in production mode.

---

## 📞 SUPPORT

For issues:
1. Check logs: `storage/logs/laravel.log`
2. Check Midtrans dashboard for transaction status
3. Verify .env configuration
4. Run database migrations: `php artisan migrate`
5. Clear cache: `php artisan cache:clear`

