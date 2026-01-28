<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Link;
use App\Models\Click;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $links = Link::where('user_id', $user->id)
            ->withCount('clicks')
            ->get();

        $totalClicks = Click::whereHas('link', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        $totalLinks = $links->count();
        $activeLinks = $links->where('is_active', 1)->count();

        // ============================
        // GRAFIK KLIK 7 HARI TERAKHIR
        // ============================
        $clicksPerDay = Click::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->whereHas('link', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->translatedFormat('D');

            $found = $clicksPerDay->firstWhere('date', $date);
            $data[] = $found ? $found->total : 0;
        }

        // ⬇️ PENTING: nilai tertinggi untuk tinggi bar
        $maxClick = max($data);

        return view('dashboard.index', compact(
            'user',
            'links',
            'totalClicks',
            'totalLinks',
            'activeLinks',
            'labels',
            'data',
            'maxClick'
        ));
    }
}
        