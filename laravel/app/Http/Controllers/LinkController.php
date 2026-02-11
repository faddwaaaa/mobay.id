<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Link;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LinkController extends Controller
{
    /**
     * Halaman manajemen link
     */
    public function index()
    {
        $pages = Page::where('user_id', Auth::id())
            ->orderBy('position')
            ->get();

        return view('dashboard.links.index', compact('pages'));
    }

    /**
     * Redirect short link dan catat klik
     */
public function redirect($short_code)
{
    // LOG: Cek apakah method ini dipanggil
    \Log::info('Redirect called', ['short_code' => $short_code]);

    // Cari link berdasarkan short_code
    $link = Link::where('short_code', $short_code)
        ->where('is_active', 1)
        ->firstOrFail();

    \Log::info('Link found', ['link_id' => $link->id, 'url' => $link->original_url]);

    // Deteksi device type
    $userAgent = request()->userAgent();
    $deviceType = $this->detectDevice($userAgent);

    // Deteksi referrer source
    $referrer = request()->header('referer');
    $referrerSource = $this->detectReferrerSource($referrer);

    \Log::info('About to insert click', [
        'link_id' => $link->id,
        'ip' => request()->ip(),
        'device' => $deviceType,
        'referrer_source' => $referrerSource
    ]);

    // Catat klik ke database
    try {
        DB::table('clicks')->insert([
            'link_id' => $link->id,
            'ip_address' => request()->ip(),
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'referrer' => $referrer,
            'referrer_source' => $referrerSource,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        \Log::info('Click inserted successfully');
    } catch (\Exception $e) {
        \Log::error('Failed to insert click', ['error' => $e->getMessage()]);
    }

    // Redirect ke URL asli
    return redirect($link->original_url);
}

    /**
     * Deteksi jenis device
     */
    private function detectDevice($userAgent)
    {
        $userAgent = strtolower($userAgent);

        if (preg_match('/mobile|android|iphone|ipod|blackberry|iemobile|opera mini/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Deteksi sumber referrer
     */
    private function detectReferrerSource($referrer)
    {
        if (empty($referrer)) {
            return 'direct';
        }

        $referrer = strtolower($referrer);

        // Social Media
        if (strpos($referrer, 'facebook.com') !== false || strpos($referrer, 'fb.com') !== false) {
            return 'facebook';
        }
        if (strpos($referrer, 'instagram.com') !== false) {
            return 'instagram';
        }
        if (strpos($referrer, 'twitter.com') !== false || strpos($referrer, 'x.com') !== false) {
            return 'twitter';
        }
        if (strpos($referrer, 'linkedin.com') !== false) {
            return 'linkedin';
        }
        if (strpos($referrer, 'tiktok.com') !== false) {
            return 'tiktok';
        }
        if (strpos($referrer, 'youtube.com') !== false) {
            return 'youtube';
        }
        if (strpos($referrer, 'whatsapp.com') !== false || strpos($referrer, 'wa.me') !== false) {
            return 'whatsapp';
        }
        if (strpos($referrer, 't.me') !== false || strpos($referrer, 'telegram') !== false) {
            return 'telegram';
        }

        // Search Engines
        if (strpos($referrer, 'google.com') !== false) {
            return 'google';
        }
        if (strpos($referrer, 'bing.com') !== false) {
            return 'bing';
        }
        if (strpos($referrer, 'yahoo.com') !== false) {
            return 'yahoo';
        }

        return 'other';
    }
}