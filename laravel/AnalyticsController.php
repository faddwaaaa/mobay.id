<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function show(Request $request)
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
        $totalClicks = DB::table('payou_id_clicks as c')
            ->join('payou_id_links as l', 'c.link_id', '=', 'l.id')
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->count();

        /** PREVIOUS PERIOD */
        $prevStart = $start->copy()->subDays($range);
        $prevClicks = DB::table('payou_id_clicks as c')
            ->join('payou_id_links as l', 'c.link_id', '=', 'l.id')
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$prevStart, $start])
            ->count();

        $clickGrowth = $prevClicks > 0
            ? round((($totalClicks - $prevClicks) / $prevClicks) * 100, 1)
            : ($totalClicks > 0 ? 100 : 0);

        /** ACTIVE LINKS */
        $activeLinks = DB::table('payou_id_links')
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->count();

        /** UNIQUE VISITORS */
        $uniqueVisitors = DB::table('payou_id_clicks as c')
            ->join('payou_id_links as l', 'c.link_id', '=', 'l.id')
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->distinct('c.ip_address')
            ->count('c.ip_address');

        /** CTR (VALID) */
        $impressions = DB::table('payou_id_links')
            ->where('user_id', $userId)
            ->sum('views');

        $ctr = $impressions > 0
            ? round(($totalClicks / $impressions) * 100, 2)
            : 0;

        /** CLICKS PER DAY */
        $daily = DB::table('payou_id_clicks as c')
            ->join('payou_id_links as l', 'c.link_id', '=', 'l.id')
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->selectRaw('DATE(c.created_at) as date, COUNT(*) as clicks, COUNT(DISTINCT c.ip_address) as visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        /** DEVICE STATS (REAL) */
        $devices = DB::table('payou_id_clicks as c')
            ->join('payou_id_links as l', 'c.link_id', '=', 'l.id')
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->select('c.device_type', DB::raw('COUNT(*) as total'))
            ->groupBy('c.device_type')
            ->pluck('total', 'device_type');

        $deviceTotal = $devices->sum() ?: 1;

        /** TOP LINKS */
        $topLinks = DB::table('payou_id_links as l')
            ->leftJoin('payou_id_clicks as c', function ($q) use ($start, $end) {
                $q->on('l.id', '=', 'c.link_id')
                  ->whereBetween('c.created_at', [$start, $end]);
            })
            ->where('l.user_id', $userId)
            ->select('l.id', 'l.title', 'l.short_code', DB::raw('COUNT(c.id) as clicks'))
            ->groupBy('l.id')
            ->orderByDesc('clicks')
            ->limit(5)
            ->get();

        /** TRAFFIC SOURCES */
        $sources = DB::table('payou_id_clicks as c')
            ->join('payou_id_links as l', 'c.link_id', '=', 'l.id')
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->select('c.referrer_source', DB::raw('COUNT(*) as total'))
            ->groupBy('c.referrer_source')
            ->orderByDesc('total')
            ->get();

        /** HOURLY */
        $hourly = DB::table('payou_id_clicks as c')
            ->join('payou_id_links as l', 'c.link_id', '=', 'l.id')
            ->where('l.user_id', $userId)
            ->whereBetween('c.created_at', [$start, $end])
            ->selectRaw('HOUR(c.created_at) as h, COUNT(*) as t')
            ->groupBy('h')
            ->pluck('t', 'h');

        return [
            'totalClicks' => $totalClicks,
            'activeLinks' => $activeLinks,
            'uniqueVisitors' => $uniqueVisitors,
            'ctr' => $ctr,
            'clicksPerDay' => $daily,
            'deviceStats' => [
                'mobile' => round(($devices['mobile'] ?? 0) / $deviceTotal * 100),
                'desktop' => round(($devices['desktop'] ?? 0) / $deviceTotal * 100),
                'tablet' => round(($devices['tablet'] ?? 0) / $deviceTotal * 100),
            ],
            'topLinks' => $topLinks,
            'trafficSources' => $sources,
            'hourlyActivity' => array_map(fn ($h) => $hourly[$h] ?? 0, range(0, 23)),
            'growthStats' => [
                'clicks' => $clickGrowth
            ]
        ];
    }
}
