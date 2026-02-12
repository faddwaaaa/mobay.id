<?php
// app/Http/Controllers/LinkRedirectController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Link;
use App\Models\Click;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LinkRedirectController extends Controller
{
    /**
     * Redirect dan track klik berdasarkan username
     * URL: http://localhost:8000/go/asadtevy94
     */
    public function redirect(Request $request, $username)
    {
        // Cari user berdasarkan username atau email prefix
        $user = User::where('username', $username)
                    ->orWhereRaw("SUBSTRING_INDEX(email, '@', 1) = ?", [$username])
                    ->first();

        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }

        // Ambil link terbaru yang aktif dari user ini
        $link = Link::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

        if (!$link) {
            // Jika tidak ada link, redirect ke public profile
            return redirect()->route('public.profile', ['username' => $username]);
        }

        // Track klik - REAL TIME
        $this->trackClick($request, $link, $user);

        // Redirect ke URL tujuan
        return redirect()->away($link->url);
    }

    private function trackClick(Request $request, Link $link, User $user)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent() ?? 'Unknown';
        $referer = $request->header('referer');

        try {
            DB::transaction(function () use ($link, $user, $ipAddress, $userAgent, $referer) {
                // 1. Simpan click record - DATA REAL TIME
                Click::create([
                    'link_id' => $link->id,
                    'user_id' => $user->id,
                    'ip_address' => substr($ipAddress, 0, 255),
                    'user_agent' => substr($userAgent, 0, 255),
                    'referer' => $referer ? substr($referer, 0, 255) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 2. Update views counter (+1)
                $link->increment('views');

                // 3. Track unique session
                $this->trackSession($user, $ipAddress, $userAgent);
            });
        } catch (\Exception $e) {
            Log::error('Failed to track click: ' . $e->getMessage());
        }
    }

    private function trackSession(User $user, $ipAddress, $userAgent)
    {
        $today = now()->startOfDay();
        
        $existingSession = Session::where('user_id', $user->id)
                                  ->where('ip_address', $ipAddress)
                                  ->where('created_at', '>=', $today)
                                  ->first();

        if (!$existingSession) {
            Session::create([
                'user_id' => $user->id,
                'ip_address' => substr($ipAddress, 0, 45),
                'user_agent' => substr($userAgent ?? '', 0, 255),
                'payload' => '',
                'last_activity' => time(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $existingSession->update([
                'last_activity' => time(),
                'updated_at' => now(),
            ]);
        }
    }
}
// ```

// ## Cara Menggunakan:

// ### Share link untuk tracking:
// ```
// http://localhost:8000/go/asadtevy94
// ```

// ### Share link public profile biasa:
// ```
// http://localhost:8000/asadtevy94