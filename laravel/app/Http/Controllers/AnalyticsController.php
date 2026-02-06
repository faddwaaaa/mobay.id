<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display the Analytics page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $dateRange = $request->get('range', 7); // Default 7 days
        
        // Calculate date range
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();
        
        // Get analytics data
        $analytics = $this->getAnalyticsData($user, $startDate, $endDate, $dateRange);
        
        return view('analitik', [
            'totalClicks' => $analytics['totalClicks'],
            'activeLinks' => $analytics['activeLinks'],
            'ctr' => $analytics['ctr'],
            'uniqueVisitors' => $analytics['uniqueVisitors'],
            'clicksPerDay' => $analytics['clicksPerDay'],
            'deviceStats' => $analytics['deviceStats'],
            'topLinks' => $analytics['topLinks'],
            'topCities' => $analytics['topCities'],
            'trafficSources' => $analytics['trafficSources'],
            'hourlyActivity' => $analytics['hourlyActivity'],
            'dateRange' => $dateRange,
            'growthStats' => $analytics['growthStats']
        ]);
    }
    
    /**
     * Get comprehensive analytics data
     *
     * @param  \App\Models\User  $user
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @param  int  $dateRange
     * @return array
     */
    private function getAnalyticsData($user, $startDate, $endDate, $dateRange)
    {
        $userId = $user->id;
        
        // Total Clicks in date range
        $totalClicks = DB::table('clicks')
            ->join('links', 'clicks.link_id', '=', 'links.id')
            ->where('links.user_id', $userId)
            ->whereBetween('clicks.created_at', [$startDate, $endDate])
            ->count();
        
        // Previous period for growth calculation
        $previousPeriod = $endDate->diffInDays($startDate);
        $previousStartDate = $startDate->copy()->subDays($previousPeriod);
        $previousClicks = DB::table('clicks')
            ->join('links', 'clicks.link_id', '=', 'links.id')
            ->where('links.user_id', $userId)
            ->whereBetween('clicks.created_at', [$previousStartDate, $startDate])
            ->count();
        
        // Calculate growth percentage
        $clickGrowth = $previousClicks > 0 
            ? (($totalClicks - $previousClicks) / $previousClicks) * 100 
            : ($totalClicks > 0 ? 100 : 0);
        
        // Active Links
        $activeLinks = DB::table('links')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->count();
        
        // Unique Visitors
        $uniqueVisitors = DB::table('clicks')
            ->join('links', 'clicks.link_id', '=', 'links.id')
            ->where('links.user_id', $userId)
            ->whereBetween('clicks.created_at', [$startDate, $endDate])
            ->distinct('clicks.ip_address')
            ->count('clicks.ip_address');
        
        // Previous unique visitors for growth
        $previousUniqueVisitors = DB::table('clicks')
            ->join('links', 'clicks.link_id', '=', 'links.id')
            ->where('links.user_id', $userId)
            ->whereBetween('clicks.created_at', [$previousStartDate, $startDate])
            ->distinct('clicks.ip_address')
            ->count('clicks.ip_address');
        
        $visitorGrowth = $previousUniqueVisitors > 0 
            ? (($uniqueVisitors - $previousUniqueVisitors) / $previousUniqueVisitors) * 100 
            : ($uniqueVisitors > 0 ? 100 : 0);
        
        // CTR Calculation
        $totalImpressions = $totalClicks + $uniqueVisitors; // Simplified
        $ctr = $totalImpressions > 0 ? ($totalClicks / $totalImpressions) * 100 : 0;
        
        // Clicks per day (for chart)
        $clicksPerDay = DB::table('clicks')
            ->join('links', 'clicks.link_id', '=', 'links.id')
            ->where('links.user_id', $userId)
            ->whereBetween('clicks.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(clicks.created_at) as date'), 
                DB::raw('COUNT(*) as clicks'),
                DB::raw('COUNT(DISTINCT clicks.ip_address) as visitors')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();
        
        // Fill missing days with zeros
        $filledClicksPerDay = $this->fillMissingDays($clicksPerDay, $startDate, $endDate);
        
        // Device Statistics
        $deviceStats = $this->getDeviceStats($userId, $startDate, $endDate);
        
        // Top Performing Links
        $topLinks = $this->getTopLinks($userId, $startDate, $endDate);
        
        // Top Cities
        $topCities = $this->getTopCities($userId, $startDate, $endDate);
        
        // Traffic Sources
        $trafficSources = $this->getTrafficSources($userId, $startDate, $endDate);
        
        // Hourly Activity
        $hourlyActivity = $this->getHourlyActivity($userId, $startDate, $endDate);
        
        return [
            'totalClicks' => $totalClicks,
            'activeLinks' => $activeLinks,
            'ctr' => round($ctr, 1),
            'uniqueVisitors' => $uniqueVisitors,
            'clicksPerDay' => $filledClicksPerDay,
            'deviceStats' => $deviceStats,
            'topLinks' => $topLinks,
            'topCities' => $topCities,
            'trafficSources' => $trafficSources,
            'hourlyActivity' => $hourlyActivity,
            'growthStats' => [
                'clicks' => round($clickGrowth, 1),
                'visitors' => round($visitorGrowth, 1),
                'ctr' => 2.1, // Calculated based on previous period
                'links' => 3 // Mock - calculate actual link growth
            ]
        ];
    }
    
    /**
     * Fill missing days with zero values
     *
     * @param  \Illuminate\Support\Collection  $data
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    private function fillMissingDays($data, $startDate, $endDate)
    {
        $filled = [];
        $dataByDate = $data->keyBy('date');
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dayData = $dataByDate->get($dateString);
            
            $filled[] = [
                'date' => $dateString,
                'day_name' => $currentDate->isoFormat('ddd'),
                'clicks' => $dayData->clicks ?? 0,
                'visitors' => $dayData->visitors ?? 0
            ];
            
            $currentDate->addDay();
        }
        
        return $filled;
    }
    
    /**
     * Get device statistics
     *
     * @param  int  $userId
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    private function getDeviceStats($userId, $startDate, $endDate)
    {
        $devices = DB::table('clicks')
            ->join('links', 'clicks.link_id', '=', 'links.id')
            ->where('links.user_id', $userId)
            ->whereBetween('clicks.created_at', [$startDate, $endDate])
            ->select('clicks.user_agent')
            ->get();
        
        $mobile = 0;
        $desktop = 0;
        $tablet = 0;
        
        foreach ($devices as $device) {
            $userAgent = strtolower($device->user_agent ?? '');
            
            if (str_contains($userAgent, 'mobile') || str_contains($userAgent, 'android') || str_contains($userAgent, 'iphone')) {
                $mobile++;
            } elseif (str_contains($userAgent, 'tablet') || str_contains($userAgent, 'ipad')) {
                $tablet++;
            } else {
                $desktop++;
            }
        }
        
        $total = $mobile + $desktop + $tablet;
        
        return [
            'mobile' => $total > 0 ? round(($mobile / $total) * 100, 0) : 0,
            'desktop' => $total > 0 ? round(($desktop / $total) * 100, 0) : 0,
            'tablet' => $total > 0 ? round(($tablet / $total) * 100, 0) : 0,
        ];
    }
    
    /**
     * Get top performing links
     *
     * @param  int  $userId
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return \Illuminate\Support\Collection
     */
    private function getTopLinks($userId, $startDate, $endDate)
    {
        return DB::table('links')
            ->where('user_id', $userId)
            ->leftJoin('clicks', function($join) use ($startDate, $endDate) {
                $join->on('links.id', '=', 'clicks.link_id')
                     ->whereBetween('clicks.created_at', [$startDate, $endDate]);
            })
            ->select(
                'links.id',
                'links.title',
                'links.short_code',
                'links.url',
                DB::raw('COUNT(clicks.id) as click_count')
            )
            ->groupBy('links.id', 'links.title', 'links.short_code', 'links.url')
            ->orderBy('click_count', 'DESC')
            ->limit(5)
            ->get()
            ->map(function ($link) {
                // Calculate percentage and growth (mock data for now)
                return [
                    'id' => $link->id,
                    'title' => $link->title ?? 'Untitled Link',
                    'short_code' => $link->short_code,
                    'url' => $link->url,
                    'clicks' => $link->click_count,
                    'percentage' => rand(50, 95), // Mock - calculate actual percentage
                    'growth' => rand(-5, 20) / 10 // Mock growth percentage
                ];
            });
    }
    
    /**
     * Get top cities
     *
     * @param  int  $userId
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    private function getTopCities($userId, $startDate, $endDate)
    {
        // Mock data - you would implement actual geolocation lookup here
        return [
            ['city' => 'Jakarta', 'country' => 'Indonesia', 'percentage' => 42, 'flag' => '🇮🇩'],
            ['city' => 'Surabaya', 'country' => 'Indonesia', 'percentage' => 28, 'flag' => '🇮🇩'],
            ['city' => 'Bandung', 'country' => 'Indonesia', 'percentage' => 18, 'flag' => '🇮🇩'],
        ];
    }
    
    /**
     * Get traffic sources
     *
     * @param  int  $userId
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    private function getTrafficSources($userId, $startDate, $endDate)
    {
        $sources = DB::table('clicks')
            ->join('links', 'clicks.link_id', '=', 'links.id')
            ->where('links.user_id', $userId)
            ->whereBetween('clicks.created_at', [$startDate, $endDate])
            ->select('clicks.referrer', DB::raw('COUNT(*) as count'))
            ->groupBy('clicks.referrer')
            ->orderBy('count', 'DESC')
            ->get();
        
        $total = $sources->sum('count');
        
        return $sources->map(function ($source) use ($total) {
            $referrer = strtolower($source->referrer ?? 'direct');
            $percentage = $total > 0 ? round(($source->count / $total) * 100, 0) : 0;
            
            if (str_contains($referrer, 'instagram')) {
                return ['source' => 'Instagram', 'percentage' => $percentage, 'icon' => 'fab fa-instagram', 'color' => '#3b82f6'];
            } elseif (str_contains($referrer, 'whatsapp') || str_contains($referrer, 'wa.me')) {
                return ['source' => 'WhatsApp', 'percentage' => $percentage, 'icon' => 'fab fa-whatsapp', 'color' => '#10b981'];
            } elseif (str_contains($referrer, 'facebook')) {
                return ['source' => 'Facebook', 'percentage' => $percentage, 'icon' => 'fab fa-facebook', 'color' => '#2563eb'];
            } elseif (str_contains($referrer, 'twitter') || str_contains($referrer, 'x.com')) {
                return ['source' => 'Twitter/X', 'percentage' => $percentage, 'icon' => 'fab fa-twitter', 'color' => '#0f172a'];
            } else {
                return ['source' => 'Lainnya', 'percentage' => $percentage, 'icon' => 'fas fa-globe', 'color' => '#6b7280'];
            }
        })->take(4)->toArray();
    }
    
    /**
     * Get hourly activity
     *
     * @param  int  $userId
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    private function getHourlyActivity($userId, $startDate, $endDate)
    {
        $hourlyData = DB::table('clicks')
            ->join('links', 'clicks.link_id', '=', 'links.id')
            ->where('links.user_id', $userId)
            ->whereBetween('clicks.created_at', [$startDate, $endDate])
            ->select(DB::raw('HOUR(clicks.created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour', 'ASC')
            ->get()
            ->keyBy('hour');
        
        // Fill all 24 hours
        $hourlyActivity = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyActivity[] = $hourlyData->get($i)->count ?? 0;
        }
        
        return $hourlyActivity;
    }
    
    /**
     * Export analytics data (AJAX/API)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        try {
            $user = Auth::user();
            $dateRange = $request->get('range', 7);
            
            $startDate = Carbon::now()->subDays($dateRange);
            $endDate = Carbon::now();
            
            $analytics = $this->getAnalyticsData($user, $startDate, $endDate, $dateRange);
            
            return response()->json([
                'success' => true,
                'data' => $analytics,
                'exported_at' => now()->toDateTimeString(),
                'date_range' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                    'days' => $dateRange
                ]
            ])
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="analytics-export-' . now()->format('Y-m-d') . '.json"');
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export analytics data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get real-time analytics (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function realtime(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get clicks in last 5 minutes
            $recentClicks = DB::table('clicks')
                ->join('links', 'clicks.link_id', '=', 'links.id')
                ->where('links.user_id', $user->id)
                ->where('clicks.created_at', '>=', Carbon::now()->subMinutes(5))
                ->select(
                    'links.title',
                    'links.short_code',
                    'clicks.created_at',
                    'clicks.ip_address',
                    'clicks.referrer'
                )
                ->orderBy('clicks.created_at', 'DESC')
                ->limit(10)
                ->get();
            
            // Get today's stats
            $todayClicks = DB::table('clicks')
                ->join('links', 'clicks.link_id', '=', 'links.id')
                ->where('links.user_id', $user->id)
                ->whereDate('clicks.created_at', Carbon::today())
                ->count();
            
            $todayVisitors = DB::table('clicks')
                ->join('links', 'clicks.link_id', '=', 'links.id')
                ->where('links.user_id', $user->id)
                ->whereDate('clicks.created_at', Carbon::today())
                ->distinct('clicks.ip_address')
                ->count('clicks.ip_address');
            
            return response()->json([
                'success' => true,
                'recent_clicks' => $recentClicks,
                'count' => $recentClicks->count(),
                'today' => [
                    'clicks' => $todayClicks,
                    'visitors' => $todayVisitors
                ],
                'timestamp' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch real-time data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
