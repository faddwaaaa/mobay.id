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
        $days = $request->get('days', 7);
        
        // Date range
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();
        
        // ===== REAL DATA FROM DATABASE =====
        
        // Total Clicks (from clicks table)
        $totalClicks = DB::table('clicks')
            ->join('links', 'clicks.link_id', '=', 'links.id')
            ->where('links.user_id', $user->id)
            ->whereBetween('clicks.created_at', [$startDate, $endDate])
            ->count();
        
        // Total Links (from links table) - NO is_active column
        $totalLinks = DB::table('links')
            ->where('user_id', $user->id)
            ->count();
        
        // Unique Visitors (distinct IP from clicks table)
        $uniqueVisitors = DB::table('clicks')
            ->join('links', 'clicks.link_id', '=', 'links.id')
            ->where('links.user_id', $user->id)
            ->whereBetween('clicks.created_at', [$startDate, $endDate])
            ->distinct('clicks.ip_address')
            ->count('clicks.ip_address');
        
        // CTR calculation (simplified)
        $ctr = $totalClicks > 0 ? ($uniqueVisitors / $totalClicks) * 100 : 0;
        
        // Top Links (with click count)
        $topLinks = DB::table('links')
            ->where('user_id', $user->id)
            ->leftJoin('clicks', function($join) use ($startDate, $endDate) {
                $join->on('links.id', '=', 'clicks.link_id')
                     ->whereBetween('clicks.created_at', [$startDate, $endDate]);
            })
            ->select(
                'links.id',
                'links.title',
                'links.short_code',
                'links.url',
                DB::raw('COUNT(clicks.id) as clicks_count')
            )
            ->groupBy('links.id', 'links.title', 'links.short_code', 'links.url')
            ->orderBy('clicks_count', 'DESC')
            ->limit(5)
            ->get();
        
        // Device Stats (from user_agent in clicks table)
        $deviceStats = $this->getDeviceStats($user->id, $startDate, $endDate);
        
        // Traffic Sources (from referrer in clicks table)
        $trafficSources = $this->getTrafficSources($user->id, $startDate, $endDate);
        
        // Clicks per day (for chart)
        $clicksPerDay = $this->getClicksPerDay($user->id, $startDate, $endDate);
        
        return view('analitik', compact(
            'totalClicks',
            'totalLinks',
            'uniqueVisitors',
            'ctr',
            'topLinks',
            'deviceStats',
            'trafficSources',
            'clicksPerDay'
        ));
    }
    
    /**
     * Get device statistics from user_agent column
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
            $ua = strtolower($device->user_agent ?? '');
            
            if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
                $mobile++;
            } elseif (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
                $tablet++;
            } else {
                $desktop++;
            }
        }
        
        $total = $mobile + $desktop + $tablet;
        
        return [
            'mobile' => $total > 0 ? round(($mobile / $total) * 100) : 0,
            'desktop' => $total > 0 ? round(($desktop / $total) * 100) : 0,
            'tablet' => $total > 0 ? round(($tablet / $total) * 100) : 0,
        ];
    }
    
    /**
     * Get traffic sources from referrer column
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
            ->limit(4)
            ->get();
        
        $total = $sources->sum('count');
        
        $result = [];
        foreach ($sources as $source) {
            $ref = strtolower($source->referrer ?? '');
            $percentage = $total > 0 ? round(($source->count / $total) * 100) : 0;
            
            if (str_contains($ref, 'instagram')) {
                $result[] = ['name' => 'Instagram', 'percentage' => $percentage, 'icon' => 'fab fa-instagram'];
            } elseif (str_contains($ref, 'whatsapp') || str_contains($ref, 'wa.me')) {
                $result[] = ['name' => 'WhatsApp', 'percentage' => $percentage, 'icon' => 'fab fa-whatsapp'];
            } elseif (str_contains($ref, 'facebook')) {
                $result[] = ['name' => 'Facebook', 'percentage' => $percentage, 'icon' => 'fab fa-facebook'];
            } else {
                $result[] = ['name' => 'Lainnya', 'percentage' => $percentage, 'icon' => 'fas fa-globe'];
            }
        }
        
        return $result;
    }
    
    /**
     * Get clicks per day for chart
     */
    private function getClicksPerDay($userId, $startDate, $endDate)
    {
        $data = DB::table('clicks')
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
        
        // Fill missing days
        $result = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $found = $data->firstWhere('date', $dateStr);
            
            $result[] = [
                'date' => $dateStr,
                'day' => $current->format('D'),
                'clicks' => $found->clicks ?? 0,
                'visitors' => $found->visitors ?? 0
            ];
            
            $current->addDay();
        }
        
        return $result;
    }
}