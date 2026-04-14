# MIDTRANS INTEGRATION - QUICK REFERENCE

## Files Created/Modified Summary

### 🆕 NEW FILES

**Migrations:**
- `database/migrations/2026_01_30_000000_add_balance_to_users_table.php`
- `database/migrations/2026_01_30_000001_create_transactions_table.php`
- `database/migrations/2026_01_30_000002_create_withdrawals_table.php`

**Models:**
- `app/Models/Transaction.php`
- `app/Models/Withdrawal.php`

**Services:**
- `app/Services/MidtransService.php`
- `app/Services/PaymentService.php`

**Controllers:**
- `app/Http/Controllers/TransactionController.php`
- `app/Http/Controllers/CallbackController.php`

**Requests:**
- `app/Http/Requests/TopUpRequest.php`
- `app/Http/Requests/WithdrawRequest.php`

**Config:**
- `config/midtrans.php`

**Documentation:**
- `MIDTRANS_INTEGRATION.md`
- `MIDTRANS_IMPLEMENTATION_GUIDE.md`

### 🔄 MODIFIED FILES

- `app/Models/User.php` - Added balance field, transactions & withdrawals relationships
- `app/Http/Controllers/DashboardController.php` - Added payment data (balance, earnings)
- `routes/web.php` - Added payment routes
- `.env.example` - Added Midtrans configuration variables

---

## Key Classes Overview

### MidtransService
```php
$service = app(MidtransService::class);

// Create Snap transaction
$snap = $service->createSnapTransaction($orderId, $amount, $email, $name);
// Returns: ['token' => '...', 'redirect_url' => '...']

// Get transaction status
$status = $service->getTransactionStatus($orderId);

// Verify callback signature
$isValid = $service->verifyCallbackSignature($orderId, $statusCode, $amount, $key);

// Get enabled payment methods
$methods = $service->getEnabledPaymentMethods();

// Generate unique order ID
$orderId = MidtransService::generateOrderId();

// Get client key for frontend
$key = $service->getClientKey();
```

### PaymentService
```php
$service = app(PaymentService::class);

// Create top-up
$response = $service->createTopUp($user, $amount, $ipAddress);
// Returns: ['snap_token' => '...', 'redirect_url' => '...', 'transaction' => Transaction]

// Handle successful payment
$service->handleSuccessfulPayment($orderId, $transactionId, $method, $data);

// Handle failed payment
$service->handleFailedPayment($orderId, $reason);

// Handle expired transaction
$service->handleExpiredTransaction($orderId);

// Get user balance
$balance = $service->getUserBalance($user);

// Get total earned
$earned = $service->getTotalEarned($user);

// Get transaction history
$transactions = $service->getUserTransactionHistory($user);
```

### Transaction Model
```php
// Relationships
$transaction->user();        // User yang melakukan top-up

// Scopes
Transaction::successful()    // WHERE status = settlement
Transaction::pending()       // WHERE status = pending
Transaction::failed()        // WHERE status IN (failed, expired, denied)
Transaction::byStatus($status)
Transaction::forDateRange($start, $end)

// Methods
$transaction->isSuccessful()
$transaction->isPending()
$transaction->isFailed()
$transaction->formattedAmount()  // Rp 100.000

// Properties
$transaction->amount              // 100000 (in cents)
$transaction->status              // pending, settlement, failed, expired, denied, cancelled
$transaction->order_id            // PAYOU-1706594400-a1b2c3d4
$transaction->transaction_id      // Midtrans transaction ID
$transaction->payment_method      // credit_card, bank_transfer, etc
$transaction->midtrans_response   // JSON response from Midtrans
```

### Withdrawal Model
```php
// Relationships
$withdrawal->user()            // User yang menarik
$withdrawal->approvedBy()      // Admin yang approve

// Scopes
Withdrawal::pending()          // WHERE status = pending
Withdrawal::approved()         // WHERE status = approved
Withdrawal::completed()        // WHERE status = completed
Withdrawal::byStatus($status)
Withdrawal::forDateRange($start, $end)

// Methods
$withdrawal->isPending()
$withdrawal->isApproved()
$withdrawal->isCompleted()
$withdrawal->isRejected()
$withdrawal->formattedAmount()
$withdrawal->approve($adminUserId)
$withdrawal->reject($reason)

// Properties
$withdrawal->amount            // 100000 (in cents)
$withdrawal->status            // pending, approved, rejected, completed, cancelled
$withdrawal->bank_name         // e.g., "BCA"
$withdrawal->account_name      // e.g., "John Doe"
$withdrawal->account_number    // e.g., "1234567890"
$withdrawal->approved_by       // User ID of approver
$withdrawal->approved_at       // Timestamp of approval
```

---

## Database Schema

### users table (MODIFIED)
```sql
ALTER TABLE users ADD COLUMN balance BIGINT DEFAULT 0;
```

### transactions table (NEW)
```sql
CREATE TABLE transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    order_id VARCHAR(255) UNIQUE NOT NULL,
    transaction_id VARCHAR(255) UNIQUE,
    amount BIGINT NOT NULL,
    status ENUM('pending', 'settlement', 'failed', 'expired', 'denied', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(255),
    midtrans_response JSON,
    notes TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (user_id),
    INDEX (status),
    INDEX (created_at)
);
```

### withdrawals table (NEW)
```sql
CREATE TABLE withdrawals (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    amount BIGINT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    bank_name VARCHAR(255),
    account_name VARCHAR(255),
    account_number VARCHAR(255),
    approved_by BIGINT,
    approved_at TIMESTAMP,
    notes TEXT,
    rejection_reason TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (user_id),
    INDEX (status),
    INDEX (created_at)
);
```

---

## Routes Summary

### Frontend Routes (Auth Required)
```
GET    /dashboard                           - Dashboard dengan balance
GET    /dashboard/topup                     - Form top-up
GET    /dashboard/topup/success             - Success page
GET    /dashboard/topup/error               - Error page
GET    /dashboard/topup/pending             - Pending page
GET    /dashboard/withdraw                  - Form withdraw
```

### API Routes (Auth Required)
```
POST   /api/topup                           - Create top-up
GET    /api/transactions                    - Get transaction history
POST   /api/withdraw                        - Create withdrawal
GET    /api/withdrawals                     - Get withdrawal history
GET    /api/dashboard/stats                 - Get dashboard stats
```

### Public Routes (No Auth)
```
POST   /api/callback/midtrans               - Midtrans webhook (CRITICAL)
```

---

## Transaction Status Flow

### Top-Up (Midtrans)
```
pending → settlement (successful)
       ├→ failed
       ├→ expired
       ├→ denied
       └→ cancelled
```

### Withdrawal (Manual)
```
pending → approved → completed
       ├→ rejected
       └→ cancelled
```

---

## Configuration (.env)

```env
# Enable payment system
MIDTRANS_ENABLED=true

# Midtrans credentials (get from dashboard)
MIDTRANS_MERCHANT_ID=G123456
MIDTRANS_CLIENT_KEY=VT-client-xxxxxxxxxxxxxxxx
MIDTRANS_SERVER_KEY=VT-server-xxxxxxxxxxxxxxxx

# Environment: false = sandbox, true = production
MIDTRANS_IS_PRODUCTION=false

# Callback & redirect URLs
MIDTRANS_CALLBACK_URL=http://localhost:8000/api/callback/midtrans
MIDTRANS_FINISH_URL=http://localhost:8000/dashboard/topup/success
MIDTRANS_ERROR_URL=http://localhost:8000/dashboard/topup/error
MIDTRANS_UNFINISH_URL=http://localhost:8000/dashboard/topup/pending
```

---

## Implementation Checklist

- [ ] Run migrations: `php artisan migrate`
- [ ] Copy Midtrans keys to `.env`
- [ ] Test top-up form loads
- [ ] Test Midtrans Snap opens
- [ ] Test payment flow (use sandbox cards)
- [ ] Verify callback received
- [ ] Check balance updated in database
- [ ] Test withdrawal form
- [ ] Check logs for errors
- [ ] Review security checklist
- [ ] Setup production environment
- [ ] Test with real transactions
- [ ] Monitor Midtrans dashboard

---

## Testing Sandbox Cards

Use these test cards in Midtrans Snap (sandbox mode):

**Credit Card Success:**
- Number: `4811111111111114`
- CVV: `123`
- Exp: `12/25`
- OTP: `123456`

**Debit Card Failure:**
- Number: `5105105105105100`
- Will fail payment

For other test methods, check Midtrans docs.

---

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| "Configuration incomplete" | Check .env has all Midtrans vars |
| Callback not received | Ensure endpoint is public (no auth middleware) |
| Balance not updating | Check callback signature verification, check logs |
| Amount validation fails | Check min/max in config/midtrans.php |
| Snap won't open | Check client_key is correct |
| Invalid signature | Check server_key is correct, verify signature logic |

---

## Next Steps

1. ✅ Run migrations
2. ✅ Configure .env with Midtrans keys
3. ✅ Create views for top-up/withdraw forms
4. ✅ Test complete flow in sandbox
5. ✅ Setup admin panel for withdrawal approvals (optional)
6. ✅ Deploy to production
7. ✅ Monitor transactions

---

## Notes

- Balance stored in users table (denormalized for fast queries)
- All sensitive operations use database transactions
- Signature verification is mandatory for callback security
- Logging enabled for all payment events
- Amounts stored in smallest currency unit (to avoid floating point)
- Transaction history never deleted (audit trail)

