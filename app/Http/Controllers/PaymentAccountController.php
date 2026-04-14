<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentAccountRequest;
use App\Models\PaymentAccount;
use App\Models\PaymentAccountAuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class PaymentAccountController extends Controller
{
    private const FREE_MAX_ACCOUNTS = 2;
    private const PRO_MAX_ACCOUNTS  = 5;
    private const PIN_RATE_KEY  = 'pin_verify:{userId}';
    private const PIN_MAX_TRIES = 3;
    private const PIN_DECAY_SEC = 300; // 5 menit

    private const BANK_NAMES = [
        'BCA'     => 'Bank Central Asia',
        'BRI'     => 'Bank Rakyat Indonesia',
        'BNI'     => 'Bank Negara Indonesia',
        'MANDIRI' => 'Bank Mandiri',
        'BSI'     => 'Bank Syariah Indonesia',
        'CIMB'    => 'CIMB Niaga',
        'DANAMON' => 'Danamon',
        'PERMATA' => 'Permata Bank',
        'GOPAY'   => 'GoPay',
        'OVO'     => 'OVO',
        'DANA'    => 'DANA',
    ];

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * Apakah mode dev/mock aktif?
     * Set PAYMENT_DEV_MODE=true di .env untuk testing lokal.
     * WAJIB false di production.
     */
    private function isDevMode(): bool
    {
        return config('app.env') !== 'production'
            && config('payment.dev_mode', false);
    }

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    public function index(): View
    {
        $user = auth()->user();
        $maxAccounts = $this->getMaxAccounts($user);
        $accounts = PaymentAccount::forUser(auth()->id())
            ->orderByDesc('is_default')
            ->orderBy('created_at')
            ->get()
            ->map(fn($a) => $a->toSafeArray());

        return view('payment.accounts', [
            'accounts'       => $accounts,
            'maxAccounts'    => $maxAccounts,
            'remainingSlots' => max(0, $maxAccounts - $accounts->count()),
            'bankList'       => self::BANK_NAMES,
            'isDevMode'      => $this->isDevMode(), // kirim ke view untuk info banner
            'isProUser'      => method_exists($user, 'isPro') ? $user->isPro() : false,
        ]);
    }

    // -----------------------------------------------------------------------
    // Store
    // -----------------------------------------------------------------------

    public function store(StorePaymentAccountRequest $request): JsonResponse
    {
        $user = auth()->user();

        // ── Validasi PIN ────────────────────────────────────────────────────
        $pinKey = str_replace('{userId}', $user->id, self::PIN_RATE_KEY);

        if (RateLimiter::tooManyAttempts($pinKey, self::PIN_MAX_TRIES)) {
            $seconds = RateLimiter::availableIn($pinKey);
            PaymentAccountAuditLog::record($user->id, 'pin_lockout');

            return response()->json([
                'success'     => false,
                'message'     => "Terlalu banyak percobaan PIN. Coba lagi dalam {$seconds} detik.",
                'locked'      => true,
                'retry_after' => $seconds,
            ], 429);
        }

        $pinValid = $this->isDevMode()
            // DEV MODE: PIN apapun diterima, atau gunakan DEV_PIN dari .env
            ? ($request->pin === config('payment.dev_pin', '123456'))
            // PRODUCTION: cek hash di database
            : ($user->pin_hash && Hash::check($request->pin, $user->pin_hash));

        if (! $pinValid) {
            RateLimiter::hit($pinKey, self::PIN_DECAY_SEC);
            $remaining = self::PIN_MAX_TRIES - RateLimiter::attempts($pinKey);
            PaymentAccountAuditLog::record($user->id, 'pin_failed');

            $message = $this->isDevMode()
                ? "PIN salah. (Dev mode: gunakan PIN " . config('payment.dev_pin', '123456') . "). Sisa: {$remaining}"
                : "PIN salah. Sisa percobaan: {$remaining}";

            return response()->json([
                'success'   => false,
                'message'   => $message,
                'remaining' => $remaining,
            ], 422);
        }

        RateLimiter::clear($pinKey);

        // ── Cek limit rekening ──────────────────────────────────────────────
        $maxAccounts = $this->getMaxAccounts($user);
        $count = PaymentAccount::forUser($user->id)->count();
        if ($count >= $maxAccounts) {
            return response()->json([
                'success' => false,
                'message' => 'Batas maksimal ' . $maxAccounts . ' rekening tercapai.',
            ], 422);
        }

        // ── Cek duplikasi ───────────────────────────────────────────────────
        $last4  = substr($request->account_number, -4);
        $exists = PaymentAccount::forUser($user->id)
            ->where('bank_code', strtoupper($request->bank_code))
            ->where('account_number_last4', $last4)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening ini sudah tersimpan.',
            ], 422);
        }

        // ── Set default jika perlu ──────────────────────────────────────────
        if ($request->boolean('is_default') || $count === 0) {
            PaymentAccount::forUser($user->id)->update(['is_default' => false]);
        }

        // ── Simpan ──────────────────────────────────────────────────────────
        $account = PaymentAccount::create([
            'user_id'                  => $user->id,
            'bank_code'                => strtoupper($request->bank_code),
            'bank_name'                => self::BANK_NAMES[$request->bank_code] ?? $request->bank_code,
            'account_number_encrypted' => $request->account_number,
            'account_number_last4'     => $last4,
            'account_holder_encrypted' => $request->account_holder,
            'label'                    => $request->label,
            'is_default'               => $request->boolean('is_default') || $count === 0,
            'is_verified'              => true,
        ]);

        PaymentAccountAuditLog::record($user->id, 'account_created', $account->id, [
            'bank_code' => $account->bank_code,
            'last4'     => $last4,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rekening berhasil disimpan.',
            'account' => $account->toSafeArray(),
        ]);
    }

    // -----------------------------------------------------------------------
    // Set Default
    // -----------------------------------------------------------------------

    public function setDefault(Request $request, PaymentAccount $paymentAccount): JsonResponse
    {
        $this->authorizeAccount($paymentAccount);

        PaymentAccount::forUser(auth()->id())->update(['is_default' => false]);
        $paymentAccount->update(['is_default' => true]);

        PaymentAccountAuditLog::record(auth()->id(), 'set_default', $paymentAccount->id);

        return response()->json(['success' => true, 'message' => 'Rekening utama diperbarui.']);
    }

    // -----------------------------------------------------------------------
    // Delete
    // -----------------------------------------------------------------------

    public function destroy(Request $request, PaymentAccount $paymentAccount): JsonResponse
    {
        $this->authorizeAccount($paymentAccount);
        $request->validate(['pin' => ['required', 'string', 'digits:6']]);

        $user   = auth()->user();
        $pinKey = str_replace('{userId}', $user->id, self::PIN_RATE_KEY);

        if (RateLimiter::tooManyAttempts($pinKey, self::PIN_MAX_TRIES)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun dikunci sementara. Coba lagi nanti.',
                'locked'  => true,
            ], 429);
        }

        $pinValid = $this->isDevMode()
            ? ($request->pin === config('payment.dev_pin', '123456'))
            : ($user->pin_hash && Hash::check($request->pin, $user->pin_hash));

        if (! $pinValid) {
            RateLimiter::hit($pinKey, self::PIN_DECAY_SEC);
            $remaining = self::PIN_MAX_TRIES - RateLimiter::attempts($pinKey);

            return response()->json([
                'success'   => false,
                'message'   => $this->isDevMode()
                    ? "PIN salah. (Dev: " . config('payment.dev_pin', '123456') . "). Sisa: {$remaining}"
                    : "PIN salah. Sisa percobaan: {$remaining}",
                'remaining' => $remaining,
            ], 422);
        }

        RateLimiter::clear($pinKey);

        $wasDefault = $paymentAccount->is_default;
        $bankCode   = $paymentAccount->bank_code;
        $last4      = $paymentAccount->account_number_last4;

        $paymentAccount->delete(); // soft delete

        if ($wasDefault) {
            PaymentAccount::forUser($user->id)->first()?->update(['is_default' => true]);
        }

        PaymentAccountAuditLog::record($user->id, 'account_deleted', $paymentAccount->id, [
            'bank_code' => $bankCode,
            'last4'     => $last4,
        ]);

        return response()->json(['success' => true, 'message' => 'Rekening berhasil dihapus.']);
    }

    // -----------------------------------------------------------------------
    // Verify Account (Midtrans / Mock)
    // -----------------------------------------------------------------------

    public function verifyAccount(Request $request): JsonResponse
    {
        $request->validate([
            'bank_code'      => ['required', 'string', 'max:20'],
            'account_number' => ['required', 'string', 'digits_between:10,16'],
        ]);

        $user    = auth()->user();
        $rateKey = 'verify_account:' . $user->id;

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            PaymentAccountAuditLog::record($user->id, 'verify_rate_limited');
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan verifikasi. Tunggu beberapa saat.',
            ], 429);
        }

        RateLimiter::hit($rateKey, 60);

        // ── DEV MODE: skip Midtrans, langsung return mock ───────────────────
        if ($this->isDevMode()) {
            $mockNames = [
                'BCA'     => $user->name . ' (Mock BCA)',
                'BRI'     => $user->name . ' (Mock BRI)',
                'BNI'     => $user->name . ' (Mock BNI)',
                'MANDIRI' => $user->name . ' (Mock Mandiri)',
                'BSI'     => $user->name . ' (Mock BSI)',
                'GOPAY'   => $user->name,
                'OVO'     => $user->name,
                'DANA'    => $user->name,
            ];

            $accountName = $mockNames[strtoupper($request->bank_code)] ?? $user->name;

            PaymentAccountAuditLog::record($user->id, 'account_verified_mock', null, [
                'bank_code' => $request->bank_code,
                'last4'     => substr($request->account_number, -4),
            ]);

            return response()->json([
                'success'      => true,
                'account_name' => $accountName,
                'dev_mode'     => true,
            ]);
        }

        // ── PRODUCTION: hit Midtrans ────────────────────────────────────────
        try {
            $serverKey = config('services.midtrans.server_key');
            $baseUrl   = config('services.midtrans.is_production')
                ? 'https://api.midtrans.com'
                : 'https://api.sandbox.midtrans.com';

            $response = Http::withBasicAuth($serverKey, '')
                ->timeout(10)
                ->post("{$baseUrl}/v2/bank_accounts/validate", [
                    'bank'           => strtolower($request->bank_code),
                    'account_number' => $request->account_number,
                ]);

            if ($response->successful() && isset($response['account_name'])) {
                PaymentAccountAuditLog::record($user->id, 'account_verified', null, [
                    'bank_code' => $request->bank_code,
                    'last4'     => substr($request->account_number, -4),
                ]);

                return response()->json([
                    'success'      => true,
                    'account_name' => $response['account_name'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Rekening tidak ditemukan atau tidak valid.',
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Midtrans account verification failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi layanan bank. Coba beberapa saat lagi.',
            ], 503);
        }
    }

    // -----------------------------------------------------------------------
    // Setup PIN (helper endpoint untuk development / onboarding)
    // -----------------------------------------------------------------------

    /**
     * Endpoint sementara untuk set PIN user selama development.
     * HAPUS atau lindungi dengan middleware khusus di production.
     *
     * POST /payment/setup-pin
     * Body: { "pin": "123456", "pin_confirmation": "123456" }
     */
    public function setupPin(Request $request): JsonResponse
    {
        // Blokir di production
        abort_if(app()->isProduction(), 403, 'Not available in production.');

        $request->validate([
            'pin'              => ['required', 'string', 'digits:6', 'confirmed'],
            'pin_confirmation' => ['required', 'string', 'digits:6'],
        ]);

        auth()->user()->update([
            'pin_hash' => Hash::make($request->pin),
        ]);

        return response()->json([
            'success' => true,
            'message' => "PIN berhasil diset. Gunakan PIN {$request->pin} untuk testing.",
        ]);
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    private function authorizeAccount(PaymentAccount $account): void
    {
        abort_unless($account->user_id === auth()->id(), 403, 'Forbidden');
    }

    private function getMaxAccounts($user): int
    {
        return method_exists($user, 'isPro') && $user->isPro()
            ? self::PRO_MAX_ACCOUNTS
            : self::FREE_MAX_ACCOUNTS;
    }
}
