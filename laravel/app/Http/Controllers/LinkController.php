<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LinkController extends Controller
{
    /**
     * Halaman manajemen link
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 🔹 Jika belum punya page sama sekali, buat page Utama
        if ($user->pages()->count() === 0) {
            $user->pages()->create([
                'title' => 'Utama',
                'slug' => 'utama',
                'is_default' => true,
            ]);
        }

        $pages = $user->pages()->with('blocks')->get();

        $selectedPageId = $request->query('page');

        $activePage = $selectedPageId
            ? $pages->where('id', $selectedPageId)->first()
            : $pages->where('is_default', true)->first();

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

    private function detectReferrerSource($referrer)
    {
        if (empty($referrer)) return 'direct';

        $referrer = strtolower($referrer);

        if (str_contains($referrer, 'facebook')) return 'facebook';
        if (str_contains($referrer, 'instagram')) return 'instagram';
        if (str_contains($referrer, 'twitter') || str_contains($referrer, 'x.com')) return 'twitter';
        if (str_contains($referrer, 'linkedin')) return 'linkedin';
        if (str_contains($referrer, 'tiktok')) return 'tiktok';
        if (str_contains($referrer, 'youtube')) return 'youtube';
        if (str_contains($referrer, 'whatsapp') || str_contains($referrer, 'wa.me')) return 'whatsapp';
        if (str_contains($referrer, 'telegram') || str_contains($referrer, 't.me')) return 'telegram';
        if (str_contains($referrer, 'google')) return 'google';
        if (str_contains($referrer, 'bing')) return 'bing';
        if (str_contains($referrer, 'yahoo')) return 'yahoo';

        return 'other';
    }
}
