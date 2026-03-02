<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ProfileView;
use App\Models\Product;
use App\Models\ProductSale;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil data 90 hari untuk keperluan chart (supaya filter Semua juga bisa)
        $startDate = Carbon::now()->subDays(89)->startOfDay();
        $endDate   = Carbon::now()->endOfDay();

        // ── Total Clicks (klik produk) ────────────────────────
        $totalClicks = \App\Models\ProductViews::whereHas('product', fn($q) => $q->where('user_id', $user->id))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // ── Total Profile Views ───────────────────────────────
        $totalProfileViews = ProfileView::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // ── Total Sold & Revenue ──────────────────────────────
        $salesAgg = DB::table('product_sales')
            ->join('products', 'product_sales.product_id', '=', 'products.id')
            ->where('products.user_id', $user->id)
            ->selectRaw('SUM(product_sales.qty) as total_sold, SUM(product_sales.qty * product_sales.price) as total_revenue')
            ->first();

        $totalSold    = $salesAgg->total_sold    ?? 0;
        $totalRevenue = $salesAgg->total_revenue ?? 0;

        // ── Top 5 Produk ──────────────────────────────────────
        $topProductIds = Product::where('user_id', $user->id)
            ->withCount('sales as sold')
            ->orderByDesc('sold')
            ->limit(5)
            ->pluck('id');

        $topProducts = Product::whereIn('id', $topProductIds)
            ->with('images')
            ->withCount('sales as sold')
            ->get()
            ->map(function ($p) {
                // Hitung revenue pakai query terpisah agar kolom price terbaca dengan benar
                $p->revenue = DB::table('product_sales')
                    ->where('product_id', $p->id)
                    ->sum(DB::raw('qty * price'));
                $p->image_url = $p->images->first()
                    ? asset('storage/' . $p->images->first()->image)
                    : null;
                return $p;
            })
            ->sortByDesc('sold')
            ->values();

        // ── Clicks per day (90 hari, untuk filter chart) ──────
        $clicksPerDay = $this->getClicksPerDay($user->id, $startDate, $endDate);

        return view('analitik', compact(
            'totalClicks',
            'totalProfileViews',
            'totalSold',
            'totalRevenue',
            'topProducts',
            'clicksPerDay'
        ));
    }

    private function getClicksPerDay($userId, $startDate, $endDate)
    {
        // Klik produk per hari (dari product_views)
        $clicksData = \App\Models\ProductViews::whereHas('product', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as clicks')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('clicks', 'date');

        // Profile views per hari
        $viewsData = ProfileView::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('views', 'date');

        // Sales per hari (revenue)
        $salesData = DB::table('product_sales')
            ->join('products', 'product_sales.product_id', '=', 'products.id')
            ->where('products.user_id', $userId)
            ->whereBetween('product_sales.created_at', [$startDate, $endDate])
            ->selectRaw('DATE(product_sales.created_at) as date, SUM(product_sales.qty * product_sales.price) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date');

        $result  = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateStr  = $current->format('Y-m-d');
            $result[] = [
                'date'   => $current->format('d M'),
                'clicks' => $clicksData[$dateStr] ?? 0,
                'views'  => $viewsData[$dateStr]  ?? 0,
                'sales'  => $salesData[$dateStr]  ?? 0,
            ];
            $current->addDay();
        }

        return $result;
    }
}