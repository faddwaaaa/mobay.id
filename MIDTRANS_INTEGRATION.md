# Midtrans Payment Integration - Payou.id Dashboard

## 📋 DATABASE ANALYSIS & RECOMMENDATIONS

### Current Schema Review
✅ **Existing Tables:**
- `users` - User authentication & basic info
- `user_profiles` - Extended user profile (username, bio, avatar, theme)
- `links` - Short links with analytics
- `clicks` - Click tracking
- `social_links` - Social media links

❌ **MISSING for Payment System:**

#### 1. **`balances` Table** (CRITICAL)
Current Issue: No field to store user wallet balance
```php
// Need to add to users table OR create separate balances table
// Recommended: Add balance field to users table (simpler) OR separate table (scalable)
```

#### 2. **`transactions` Table** (CRITICAL)
Current Issue: No payment transaction history
```php
// Need to track:
// - Top-up transactions (Midtrans)
// - Transaction status (pending, settlement, failed)
// - Midtrans order_id & transaction_id
// - Amount & timestamp
```

#### 3. **`withdrawals` Table** (CRITICAL)
Current Issue: No withdrawal request management
```php
// Need to track:
// - Withdrawal requests
// - Status (pending, approved, rejected, completed)
// - Bank account details (optional)
// - Admin approval workflow
```

### Database Fix Strategy

**Option 1: Balance in Users Table (RECOMMENDED for simple systems)**
```sql
ALTER TABLE users ADD COLUMN balance BIGINT DEFAULT 0 AFTER password;
```
✅ Simple
✅ Fast queries
❌ Less flexible for audit

**Option 2: Separate Balances Table (RECOMMENDED for scalable systems)**
✅ Better for audit trails
✅ Can track balance history
❌ Slightly more queries

**We'll implement Option 2 for scalability**

---

## 🗄️ REQUIRED MIGRATIONS

### 1. Add Balance to Users Table
- Keep track of current wallet balance
- Denormalized for dashboard quick access

### 2. Create Transactions Table
- Store all payment transactions
- Midtrans-specific fields (order_id, transaction_id)
- Status tracking for payment flow

### 3. Create Withdrawals Table
- Track withdrawal requests
- Manual approval workflow
- Bank account information

### 4. Add Fields to Existing Tables
- Links: Add `slug` field if missing (for short URL)
- Users: Add `balance` field

---

## 💳 PAYMENT FLOW

### TOP-UP FLOW (Using Midtrans Snap)
```
1. User clicks "Top Up" button on dashboard
2. Form: Input top-up amount
3. Validation: Amount > 0 & <= max limit
4. Backend creates transaction record (status: pending)
5. Generate unique order_id
6. Call Midtrans Snap API
7. Redirect user to Midtrans payment page
8. User pays (various methods: card, e-wallet, bank transfer, etc.)
9. Midtrans sends callback to webhook endpoint
10. Verify signature & transaction_status
11. If settlement: Update transaction status & increment user balance
12. Redirect user to success page
```

### WITHDRAW FLOW (Manual Internal Process)
```
1. User clicks "Withdraw" button on dashboard
2. Form: Input withdrawal amount & bank account (optional)
3. Validation: Balance >= amount
4. Create withdrawal request (status: pending)
5. Admin reviews & approves withdrawal (separate admin panel - NOT in scope)
6. On approval: Deduct balance & mark as completed
7. Admin manually transfer funds (offline process)
```

---

## 🔐 MIDTRANS CONFIGURATION

### .env.example Configuration
```
MIDTRANS_ENABLED=true
MIDTRANS_MERCHANT_ID=your_merchant_id
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_IS_PRODUCTION=false
```

### Environment Strategy
- Placeholder keys in `.env.example`
- Real keys added to `.env` (git-ignored)
- Use `config/midtrans.php` for centralized config

---

## 📁 REQUIRED FILES TO CREATE

1. **Migrations**
   - `add_balance_to_users_table.php`
   - `create_transactions_table.php`
   - `create_withdrawals_table.php`

2. **Models**
   - `Transaction.php` (with relationships)
   - `Withdrawal.php` (with relationships)
   - Update `User.php` with relationships

3. **Services**
   - `MidtransService.php` - Midtrans API integration
   - `PaymentService.php` - Payment logic orchestration

4. **Controllers**
   - `TransactionController.php` - Top-up logic
   - `WithdrawalController.php` - Withdraw logic
   - `CallbackController.php` - Midtrans webhook handler

5. **Requests (Validation)**
   - `TopUpRequest.php`
   - `WithdrawRequest.php`

6. **Config**
   - `config/midtrans.php` - Configuration file

7. **Routes**
   - Add payment routes to `routes/web.php`

8. **Dashboard Updates**
   - Update `DashboardController.php` to fetch balance
   - Display balance & top-up/withdraw buttons

---

## 🔑 IMPLEMENTATION HIGHLIGHTS

### Key Principles
1. **Security First**
   - Server-side signature verification for all Midtrans callbacks
   - Database transactions for balance updates
   - CSRF protection on all forms

2. **Best Practices**
   - Service layer handles Midtrans logic
   - Controller only orchestrates
   - Request validation before processing
   - Enums for transaction statuses

3. **Error Handling**
   - Graceful failure handling
   - Transaction rollback on errors
   - Detailed logging of payment events

4. **Audit Trail**
   - All transactions logged with status
   - Withdrawal approval tracking
   - Admin can view transaction history

---

## 📊 DATABASE SCHEMA FINAL

```
users (existing + new)
├── id
├── name
├── email
├── password
├── google_id
├── avatar
├── email_verified_at
├── balance (NEW) - BIGINT, default 0
├── created_at
└── updated_at

transactions (NEW)
├── id
├── user_id (FK -> users)
├── order_id (unique, from Midtrans)
├── transaction_id (from Midtrans callback)
├── amount
├── status (enum: pending, settlement, failed, expired)
├── payment_method
├── midtrans_response (JSON - full response)
├── created_at
└── updated_at

withdrawals (NEW)
├── id
├── user_id (FK -> users)
├── amount
├── status (enum: pending, approved, rejected, completed)
├── bank_name (optional)
├── account_name (optional)
├── account_number (optional)
├── approved_by (FK -> users, nullable)
├── approved_at (timestamp, nullable)
├── notes (text, nullable)
├── created_at
└── updated_at
```

---

## ✅ NEXT STEPS

1. Create migrations for new tables
2. Create Models with relationships
3. Setup `.env.example` with Midtrans config
4. Create MidtransService for API integration
5. Create PaymentService for business logic
6. Create Controllers for top-up & withdraw
7. Create Callback handler with signature verification
8. Update Dashboard to show balance
9. Add routes for payment endpoints
10. Test complete flow

---

## 🚀 TESTING CHECKLIST

- [ ] Migration runs successfully
- [ ] Models created with correct relationships
- [ ] Top-up form displays on dashboard
- [ ] Top-up creates transaction in DB with pending status
- [ ] Midtrans Snap loads correctly
- [ ] Callback endpoint receives Midtrans notification
- [ ] Signature verification works
- [ ] Balance updates after successful payment
- [ ] Withdraw form validates correctly
- [ ] Withdrawal record created in DB
- [ ] Transaction history displays on dashboard
- [ ] Error handling works gracefully

