<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Link;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class LinkController extends Controller
{
    /**
     * Halaman manajemen link
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil semua halaman dengan slug utama
        $utamaPages = $user->pages()->where('slug', 'utama')->get();

        // Kalau belum ada sama sekali → buat
        if ($utamaPages->count() === 0) {
            $user->pages()->create([
                'title' => 'Utama',
                'slug' => 'utama',
                'is_default' => true,
            ]);
        }

        // Kalau lebih dari 1 → hapus sisanya
        if ($utamaPages->count() > 1) {
            $utamaPages->slice(1)->each(function ($page) {
                $page->delete();
            });
        }

        // Pastikan hanya 1 yang jadi default
        $user->pages()
            ->where('slug', 'utama')
            ->update(['is_default' => true]);

        $user->pages()
            ->where('slug', '!=', 'utama')
            ->update(['is_default' => false]);

        $selectedPageId = $request->query('page');

        $pages = $user->pages()
            ->select(['id', 'user_id', 'title', 'slug', 'is_default', 'created_at'])
            ->orderBy('created_at')
            ->get();

        $activePageId = $selectedPageId
            ? (int) $selectedPageId
            : (int) optional(
                $pages->firstWhere('is_default', true) ?? $pages->first()
            )->id;

        $activePage = $activePageId
            ? $user->pages()
                ->select(['id', 'user_id', 'title', 'slug', 'is_default', 'created_at'])
                ->with([
                    'blocks' => function ($query) {
                        $query->select(['id', 'page_id', 'type', 'content', 'product_id', 'position'])
                            ->orderBy('position')
                            ->with([
                                'product' => function ($productQuery) {
                                    $productQuery
                                        ->select(['id', 'title', 'price', 'discount', 'product_type'])
                                        ->addSelect([
                                            'image' => ProductImage::query()
                                                ->select('image')
                                                ->whereColumn('product_id', 'products.id')
                                                ->orderBy('id')
                                                ->limit(1),
                                        ]);
                                },
                            ]);
                    },
                ])
                ->find($activePageId)
            : null;

        $products = Product::where('user_id', $user->id)
            ->select(['id', 'user_id', 'product_type', 'title', 'price', 'discount'])
            ->addSelect([
                'image' => ProductImage::query()
                    ->select('image')
                    ->whereColumn('product_id', 'products.id')
                    ->orderBy('id')
                    ->limit(1),
            ])
            ->latest()
            ->get();

        return view('dashboard.links.index', [
            'pages' => $pages,
            'activePage' => $activePage,
            'products' => $products,
        ]);
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
        if (empty($referrer)) {
            return 'direct';
        }

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
