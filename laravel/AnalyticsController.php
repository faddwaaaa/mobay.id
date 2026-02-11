<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $range = (int) $request->get('range', 7);

        $start = Carbon::now()->subDays($range)->startOfDay();
        $end   = Carbon::now()->endOfDay();

        $data = $this->analytics($user->id, $start, $end, $range);

        return view('analitik', $data + ['dateRange' => $range]);
    }

    private function analytics(int $userId, Carbon $start, Carbon $end, int $range): array
    {
        /** TOTAL CLICKS */
        $totalClicks = DB::table('clicks as c')
            ->join('links as l', 'c.link_id', '=', 'l.id')  // ← Ubah
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->count();

        /** PREVIOUS PERIOD */
        $prevStart = $start->copy()->subDays($range);
        $prevClicks = DB::table('clicks as c')
            ->join('links as l', 'c.link_id', '=', 'l.id')  // ← Ubah
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$prevStart, $start])
            ->count();

        $clickGrowth = $prevClicks > 0
            ? round((($totalClicks - $prevClicks) / $prevClicks) * 100, 1)
            : ($totalClicks > 0 ? 100 : 0);

        /** TOTAL LINKS */
        $totalLinks = DB::table('links')  // ← Ubah
            ->where('user_id', $userId)
            ->count();

        /** ACTIVE LINKS */
        $activeLinks = DB::table('links')  // ← Ubah
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->count();

        /** UNIQUE VISITORS */
        $uniqueVisitors = DB::table('clicks as c')
            ->join('links as l', 'c.link_id', '=', 'l.id')  // ← Ubah
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->distinct('c.ip_address')
            ->count('c.ip_address');

        /** CTR (Click Through Rate) */
        $impressions = DB::table('links')  // ← Ubah
            ->where('user_id', $userId)
            ->sum('views');

        $ctr = $impressions > 0
            ? round(($totalClicks / $impressions) * 100, 2)
            : 0;

        /** CLICKS PER DAY */
        $daily = DB::table('clicks as c')
            ->join('links as l', 'c.link_id', '=', 'l.id')  // ← Ubah
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->selectRaw('DATE(c.created_at) as date, COUNT(*) as clicks, COUNT(DISTINCT c.ip_address) as visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing days with zeros
        $clicksPerDay = [];
        for ($i = $range - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayData = $daily->firstWhere('date', $date);
            
            $clicksPerDay[] = [
                'day' => Carbon::parse($date)->format('d M'),
                'clicks' => $dayData->clicks ?? 0,
                'visitors' => $dayData->visitors ?? 0,
            ];
        }

        /** DEVICE STATS */
        $devices = DB::table('clicks as c')
            ->join('links as l', 'c.link_id', '=', 'l.id')  // ← Ubah
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->select('c.device_type', DB::raw('COUNT(*) as total'))
            ->groupBy('c.device_type')
            ->pluck('total', 'device_type');

        $deviceTotal = $devices->sum() ?: 1;

        /** TOP LINKS */
        $topLinks = DB::table('links as l')  // ← Ubah
            ->leftJoin('clicks as c', function ($q) use ($start, $end) {
                $q->on('l.id', '=', 'c.link_id')
                  ->whereBetween('c.created_at', [$start, $end]);
            })
            ->where('l.user_id', $userId)
            ->select(
                'l.id', 
                'l.title', 
                'l.short_code', 
                'l.created_at',
                DB::raw('COUNT(c.id) as clicks_count')
            )
            ->groupBy('l.id', 'l.title', 'l.short_code', 'l.created_at')
            ->orderByDesc('clicks_count')
            ->limit(5)
            ->get();

        /** TRAFFIC SOURCES */
        $sources = DB::table('clicks as c')
            ->join('links as l', 'c.link_id', '=', 'l.id')  // ← Ubah
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->select('c.referrer_source', DB::raw('COUNT(*) as total'))
            ->groupBy('c.referrer_source')
            ->orderByDesc('total')
            ->get();

        // Format traffic sources
        $trafficSources = $sources->map(function ($source) use ($totalClicks) {
            $percentage = $totalClicks > 0 
                ? round(($source->total / $totalClicks) * 100, 1) 
                : 0;

            return [
                'name' => $this->getSourceName($source->referrer_source),
                'description' => $this->getSourceDescription($source->referrer_source),
                'icon' => $this->getSourceIcon($source->referrer_source),
                'color' => $this->getSourceColor($source->referrer_source),
                'count' => $source->total,
                'percentage' => $percentage,
            ];
        });

        /** HOURLY ACTIVITY */
        $hourly = DB::table('clicks as c')
            ->join('links as l', 'c.link_id', '=', 'l.id')  // ← Ubah
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->selectRaw('HOUR(c.created_at) as h, COUNT(*) as t')
            ->groupBy('h')
            ->pluck('t', 'h');

        return [
            'totalClicks' => $totalClicks,
            'totalLinks' => $totalLinks,
            'activeLinks' => $activeLinks,
            'uniqueVisitors' => $uniqueVisitors,
            'ctr' => $ctr,
            'clicksPerDay' => $clicksPerDay,
            'deviceStats' => [
                'mobile' => round(($devices['mobile'] ?? 0) / $deviceTotal * 100),
                'desktop' => round(($devices['desktop'] ?? 0) / $deviceTotal * 100),
                'tablet' => round(($devices['tablet'] ?? 0) / $deviceTotal * 100),
            ],
            'topLinks' => $topLinks,
            'trafficSources' => $trafficSources,
            'hourlyActivity' => array_map(fn ($h) => $hourly[$h] ?? 0, range(0, 23)),
            'growthStats' => [
                'clicks' => $clickGrowth
            ]
        ];
    }

    private function getSourceName(string $source): string
    {
        $names = [
            'direct' => 'Direct',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'twitter' => 'Twitter',
            'linkedin' => 'LinkedIn',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            'whatsapp' => 'WhatsApp',
            'telegram' => 'Telegram',
            'google' => 'Google',
            'bing' => 'Bing',
            'yahoo' => 'Yahoo',
            'other' => 'Lainnya',
        ];
        return $names[$source] ?? ucfirst($source);
    }

    private function getSourceDescription(string $source): string
    {
        $descriptions = [
            'direct' => 'Akses langsung',
            'facebook' => 'Media sosial',
            'instagram' => 'Media sosial',
            'twitter' => 'Media sosial',
            'linkedin' => 'Media sosial',
            'tiktok' => 'Media sosial',
            'youtube' => 'Platform video',
            'whatsapp' => 'Messaging',
            'telegram' => 'Messaging',
            'google' => 'Search engine',
            'bing' => 'Search engine',
            'yahoo' => 'Search engine',
            'other' => 'Sumber lain',
        ];
        return $descriptions[$source] ?? 'Referrer';
    }

    private function getSourceIcon(string $source): string
    {
        $icons = [
            'direct' => 'fas fa-arrow-right',
            'facebook' => 'fab fa-facebook',
            'instagram' => 'fab fa-instagram',
            'twitter' => 'fab fa-twitter',
            'linkedin' => 'fab fa-linkedin',
            'tiktok' => 'fab fa-tiktok',
            'youtube' => 'fab fa-youtube',
            'whatsapp' => 'fab fa-whatsapp',
            'telegram' => 'fab fa-telegram',
            'google' => 'fab fa-google',
            'bing' => 'fab fa-microsoft',
            'yahoo' => 'fab fa-yahoo',
            'other' => 'fas fa-globe',
        ];
        return $icons[$source] ?? 'fas fa-link';
    }

    private function getSourceColor(string $source): string
    {
        $colors = [
            'direct' => '#64748b',
            'facebook' => '#1877f2',
            'instagram' => '#e4405f',
            'twitter' => '#1da1f2',
            'linkedin' => '#0a66c2',
            'tiktok' => '#000000',
            'youtube' => '#ff0000',
            'whatsapp' => '#25d366',
            'telegram' => '#0088cc',
            'google' => '#4285f4',
            'bing' => '#008373',
            'yahoo' => '#7b0099',
            'other' => '#94a3b8',
        ];
        return $colors[$source] ?? '#3b82f6';
    }
}