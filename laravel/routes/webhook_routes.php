<?php

use App\Http\Controllers\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

/**
 * Midtrans Webhook Routes
 * 
 * These routes handle callbacks from Midtrans for:
 * 1. Iris Payout Status Updates
 * 2. Transaction Notifications (for top-ups)
 * 
 * Important: These routes should NOT have CSRF protection
 * Add to app/Http/Middleware/VerifyCsrfToken.php $except array:
 * 
 * protected $except = [
 *     'webhook/midtrans/iris',
 *     'webhook/midtrans/transaction',
 * ];
 */

Route::prefix('webhook/midtrans')->name('webhook.midtrans.')->group(function () {
    // Iris Payout Callback (legacy)
    Route::post('iris', [MidtransWebhookController::class, 'handleIrisCallback'])
        ->name('iris');
    
    // Disbursement API Callback (current)
    Route::post('disbursement', [MidtransWebhookController::class, 'handleDisbursementCallback'])
        ->name('disbursement');
    
    // Transaction Notification (Snap/Core API)
    Route::post('transaction', [MidtransWebhookController::class, 'handleTransactionNotification'])
        ->name('transaction');
});
