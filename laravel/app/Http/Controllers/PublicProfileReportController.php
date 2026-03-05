<?php

namespace App\Http\Controllers;

use App\Models\ProfileReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function store(Request $request, string $username)
    {
        $reportedUser = User::where('username', $username)->firstOrFail();

        $validated = $request->validate([
            'reason' => ['required', Rule::in(self::REASONS)],
            'detail' => ['nullable', 'string', 'max:1000'],
            'page_url' => ['nullable', 'string', 'max:255'],
        ]);

        ProfileReport::create([
            'reported_user_id' => $reportedUser->id,
            'reason' => $validated['reason'],
            'detail' => $validated['detail'] ?? null,
            'reporter_ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'page_url' => $validated['page_url'] ?? $request->fullUrl(),
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim. Tim admin akan meninjau laporan Anda.',
        ]);
    }
}

