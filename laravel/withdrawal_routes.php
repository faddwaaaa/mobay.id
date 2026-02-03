<?php

use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

// Withdrawal Routes (Protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    
    // User withdrawal routes
    Route::prefix('withdrawal')->name('withdrawal.')->group(function () {
        Route::get('/', [WithdrawalController::class, 'index'])->name('index');
        Route::post('/', [WithdrawalController::class, 'store'])->name('store');
        Route::post('/{id}/cancel', [WithdrawalController::class, 'cancel'])->name('cancel');
        Route::get('/{id}/check-status', [WithdrawalController::class, 'checkStatus'])->name('check-status');
        Route::get('/banks', [WithdrawalController::class, 'getBanks'])->name('banks');
    });
});
