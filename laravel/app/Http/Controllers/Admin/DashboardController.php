<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'        => User::count(),
            'new_users_today'    => User::whereDate('created_at', today())->count(),
            'active_users'       => User::where('role', 'user')->count(),
            'suspended_users'    => User::where('is_suspended', true)->count(),
            'total_links'        => DB::table('links')->count(),
            'total_clicks_today' => DB::table('clicks')->whereDate('created_at', today())->count(),
        ];

        // Klik per hari 7 hari terakhir
        $clicksChart = DB::table('clicks')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top 5 link terklik hari ini
        $topLinks = DB::table('clicks')
            ->selectRaw('link_id, COUNT(*) as total_clicks')
            ->whereDate('created_at', today())
            ->groupBy('link_id')
            ->orderByDesc('total_clicks')
            ->limit(5)
            ->get();

        // User terbaru
        $recentUsers = User::latest()->limit(5)->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'clicksChart',
            'topLinks',
            'recentUsers'
        ));
    }
}