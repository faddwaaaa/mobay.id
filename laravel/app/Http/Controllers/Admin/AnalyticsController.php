<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Filter tanggal
        $days = $request->days ?? 30;
        $dateFrom = $request->date_from ? \Carbon\Carbon::parse($request->date_from) : now()->subDays($days - 1)->startOfDay();
        $dateTo   = $request->date_to   ? \Carbon\Carbon::parse($request->date_to)->endOfDay() : now()->endOfDay();

        // Klik harian
        $clicksDaily = DB::table('clicks')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Pertumbuhan user per bulan
        $userGrowth = User::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.analytics.index', compact('userGrowth', 'clicksDaily', 'dateFrom', 'dateTo'));
    }
}