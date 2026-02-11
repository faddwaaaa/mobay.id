<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LinkController extends Controller
{
    /**
     * Halaman manajemen link & pages
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil semua page milik user + blocks
        $pages = $user->pages()
            ->with('blocks')
            ->orderBy('created_at')
            ->get();

        $selectedPageId = $request->query('page');

        // Jika ada query page, pakai itu
        if ($selectedPageId) {
            $activePage = $pages->firstWhere('id', $selectedPageId);
        } else {
            // Default ambil page utama (is_default = true)
            $activePage = $pages->firstWhere('is_default', true);
        }

        return view('dashboard.links.index', compact('pages', 'activePage'));
    }

    /**
     * Redirect short link dan catat klik
     */
    public function redirect($short_code)
    {
        $link = Link::where('short_code', $short_code)
            ->where('is_active', 1)
            ->firstOrFail();

        $userAgent = request()->userAgent();
        $deviceType = $this->detectDevice($userAgent);
        $referrer = request()->header('referer');
        $referrerSource = $this->detectReferrerSource($referrer);

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

        return redirect($link->original_url);
    }

    /**
     * Deteksi device
     */
    private function detectDevice($userAgent)
    {
        $userAgent = strtolower($userAgent ?? '');

        if (preg_match('/mobile|android|iphone|ipod|blackberry|iemobile|opera mini/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Deteksi referrer source
     */
    private function detectReferrerSource($referrer)
    {
        if (empty($referrer)) return 'direct';

        $referrer = strtolower($referrer);

        if (str_contains($referrer, 'facebook.com')) return 'facebook';
        if (str_contains($referrer, 'instagram.com')) return 'instagram';
        if (str_contains($referrer, 'twitter.com') || str_contains($referrer, 'x.com')) return 'twitter';
        if (str_contains($referrer, 'linkedin.com')) return 'linkedin';
        if (str_contains($referrer, 'tiktok.com')) return 'tiktok';
        if (str_contains($referrer, 'youtube.com')) return 'youtube';
        if (str_contains($referrer, 'whatsapp.com') || str_contains($referrer, 'wa.me')) return 'whatsapp';
        if (str_contains($referrer, 'telegram') || str_contains($referrer, 't.me')) return 'telegram';

        if (str_contains($referrer, 'google.com')) return 'google';
        if (str_contains($referrer, 'bing.com')) return 'bing';
        if (str_contains($referrer, 'yahoo.com')) return 'yahoo';

        return 'other';
    }
}
