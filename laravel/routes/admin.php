<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\ProfileReportController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\Admin\SuspendController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ── User Management ──
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// Suspend — pakai SuspendController (POST, bukan PATCH, karena butuh form dengan alasan)
Route::post('/users/{user}/suspend',   [SuspendController::class, 'suspend'])  ->name('users.suspend');
Route::post('/users/{user}/unsuspend', [SuspendController::class, 'unsuspend'])->name('users.unsuspend');

// ── Link Management ──
Route::get('/links', [LinkController::class, 'index'])->name('links.index');
Route::get('/links/{link}', [LinkController::class, 'show'])->name('links.show');
Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('links.destroy');
Route::patch('/links/{link}/toggle', [LinkController::class, 'toggle'])->name('links.toggle');

// ── Analytics ──
Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

// ── Settings ──
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

// ── Transactions ──
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

// ── Withdrawals ──
Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
Route::get('/withdrawals/{withdrawal}', [WithdrawalController::class, 'show'])->name('withdrawals.show');
Route::patch('/withdrawals/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
Route::patch('/withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');

// ── Wallet ──
Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');

// ── Laporan Profil ──
// Urutan penting: /export dan /{report}/evidence/{index} HARUS di atas /{report}
Route::get   ('/reports',                           [ProfileReportController::class, 'index'])       ->name('reports.index');
Route::get   ('/reports/export',                    [ProfileReportController::class, 'exportCsv'])   ->name('reports.export');
Route::get   ('/reports/{report}',                  [ProfileReportController::class, 'show'])         ->name('reports.show');
Route::patch ('/reports/{report}/status',           [ProfileReportController::class, 'updateStatus'])->name('reports.updateStatus');
Route::patch ('/reports/{report}/note',             [ProfileReportController::class, 'saveNote'])    ->name('reports.saveNote');
Route::get   ('/reports/{report}/evidence',         [ProfileReportController::class, 'viewEvidence'])->name('reports.evidence');
Route::get   ('/reports/{report}/evidence/{index}', [ProfileReportController::class, 'evidenceFile'])->name('reports.evidence.file');