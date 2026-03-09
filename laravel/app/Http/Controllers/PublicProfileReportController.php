<?php

namespace App\Http\Controllers;

use App\Models\ProfileReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicProfileReportController extends Controller
{
    private const REASONS = [
        'spam',
        'scam',
        'hate_speech',
        'adult_content',
        'violence',
        'fake_account',
        'copyright',
        'other',
    ];

    // Bobot risiko per kategori (untuk menghitung skor)
    private const RISK_WEIGHTS = [
        'scam'          => 10,
        'fake_product'  => 8,
        'copyright'     => 8,
        'fake_account'  => 6,
        'violence'      => 6,
        'adult_content' => 4,
        'hate_speech'   => 4,
        'spam'          => 2,
        'other'         => 1,
    ];

    public function store(Request $request, string $username)
    {
        $reportedUser = User::where('username', $username)->firstOrFail();
        $ip           = $request->ip();

        // ── 1. Rate limit: maks 3 laporan per IP per jam ──
        $recentCount = ProfileReport::where('reporter_ip', $ip)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Anda terlalu banyak mengirim laporan. Coba lagi dalam 1 jam.',
            ], 429);
        }

        // ── 2. Deduplication: 1 laporan per IP per akun per 24 jam ──
        $alreadyReported = ProfileReport::where('reporter_ip', $ip)
            ->where('reported_user_id', $reportedUser->id)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($alreadyReported) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melaporkan akun ini dalam 24 jam terakhir.',
            ], 422);
        }

        // ── 3. Validasi input ──
        $validated = $request->validate([
            'reason'    => ['required', Rule::in(self::REASONS)],
            'detail'    => ['nullable', 'string', 'max:1000'],
            'page_url'  => ['nullable', 'string', 'max:500'],
            'evidence'  => ['nullable', 'array', 'max:5'],
            'evidence.*'=> ['file', 'max:5120', 'mimes:jpg,jpeg,png,webp,gif,pdf,mp4,mov'],
        ]);

        // ── 4. Upload bukti ke private disk ──
        $evidencePaths = [];
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $folder = 'reports/' . $reportedUser->id . '/' . date('Ymd');
                $path   = $file->store($folder, 'private');
                if ($path) {
                    $evidencePaths[] = $path;
                }
            }
        }

        // ── 5. Generate tiket unik ──
        do {
            $ticket = 'RPT-' . strtoupper(Str::random(8));
        } while (ProfileReport::where('ticket_code', $ticket)->exists());

        // ── 6. Simpan laporan ──
        $report = ProfileReport::create([
            'reported_user_id' => $reportedUser->id,
            'reason'           => $validated['reason'],
            'detail'           => $validated['detail'] ?? null,
            'reporter_ip'      => $ip,
            'user_agent'       => (string) $request->userAgent(),
            'page_url'         => $validated['page_url'] ?? $request->fullUrl(),
            'status'           => 'pending',
            'ticket_code'      => $ticket,
            'evidence_paths'   => !empty($evidencePaths) ? json_encode($evidencePaths) : null,
            'evidence_count'   => count($evidencePaths),
        ]);

        // ── 7. Cek freeze threshold: 10 IP berbeda laporan 1 akun dalam 1 jam ──
        $distinctIps = ProfileReport::where('reported_user_id', $reportedUser->id)
            ->where('created_at', '>=', now()->subHour())
            ->distinct('reporter_ip')
            ->count('reporter_ip');

        if ($distinctIps >= 10) {
            $report->update(['triggered_freeze' => 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim. Kode laporan Anda: ' . $ticket,
            'ticket'  => $ticket,
        ]);
    }
}