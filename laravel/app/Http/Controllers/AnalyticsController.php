<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductViews;
use App\Models\ProfileView;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $analytics = $this->buildAnalyticsData(Auth::user());

        return view('analitik', $analytics);
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        abort_unless($user->isPro(), 403);

        $format = strtolower((string) $request->query('format', 'csv'));
        $analytics = $this->buildAnalyticsData($user);

        if ($format === 'excel') {
            return $this->exportExcel($analytics);
        }

        return $this->exportCsv($analytics);
    }

    private function buildAnalyticsData($user): array
    {
        $startDate = Carbon::now()->subDays(89)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $currentTrendStart = Carbon::now()->subDays(6)->startOfDay();
        $previousTrendStart = Carbon::now()->subDays(13)->startOfDay();
        $previousTrendEnd = Carbon::now()->subDays(7)->endOfDay();

        $totalClicks = ProductViews::whereHas('product', fn ($q) => $q->where('user_id', $user->id))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalProfileViews = ProfileView::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $salesAgg = DB::table('product_sales')
            ->join('products', 'product_sales.product_id', '=', 'products.id')
            ->where('products.user_id', $user->id)
            ->whereBetween('product_sales.created_at', [$startDate, $endDate])
            ->selectRaw('SUM(product_sales.qty) as total_sold, SUM(product_sales.qty * product_sales.price) as total_revenue')
            ->first();

        $totalSold = (int) ($salesAgg->total_sold ?? 0);
        $totalRevenue = (float) ($salesAgg->total_revenue ?? 0);

        $topProductIds = Product::where('user_id', $user->id)
            ->withCount([
                'sales as sold' => fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]),
            ])
            ->orderByDesc('sold')
            ->limit(5)
            ->pluck('id');

        $topProducts = Product::whereIn('id', $topProductIds)
            ->with('images')
            ->withCount([
                'sales as sold' => fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]),
                'views as views_count' => fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]),
            ])
            ->get()
            ->map(function ($product) use ($startDate, $endDate) {
                $product->revenue = (float) DB::table('product_sales')
                    ->where('product_id', $product->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum(DB::raw('qty * price'));

                $product->image_url = $product->images->first()
                    ? asset('storage/' . $product->images->first()->image)
                    : null;

                return $product;
            })
            ->sortByDesc('sold')
            ->values();

        $topViewedProducts = Product::where('user_id', $user->id)
            ->with('images')
            ->withCount([
                'views as views_count' => fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]),
                'sales as sold' => fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]),
            ])
            ->orderByDesc('views_count')
            ->limit(5)
            ->get()
            ->map(function ($product) use ($startDate, $endDate) {
                $product->revenue = (float) DB::table('product_sales')
                    ->where('product_id', $product->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum(DB::raw('qty * price'));

                $product->image_url = $product->images->first()
                    ? asset('storage/' . $product->images->first()->image)
                    : null;

                return $product;
            });

        $clicksPerDay = $this->getClicksPerDay($user->id, $startDate, $endDate);
        $currentTrendPeriod = $this->getClicksPerDay($user->id, $currentTrendStart, $endDate);
        $previousTrendPeriod = $this->getClicksPerDay($user->id, $previousTrendStart, $previousTrendEnd);

        $bestTrafficDay = collect($clicksPerDay)
            ->sortByDesc(fn ($day) => ($day['views'] ?? 0) + ($day['clicks'] ?? 0))
            ->first();

        $bestRevenueDay = collect($clicksPerDay)
            ->sortByDesc('sales')
            ->first();

        $advancedStats = [
            'conversion_view_to_click' => $totalProfileViews > 0 ? round(($totalClicks / $totalProfileViews) * 100, 1) : 0,
            'conversion_click_to_sale' => $totalClicks > 0 ? round(($totalSold / $totalClicks) * 100, 1) : 0,
            'avg_revenue_per_sale' => $totalSold > 0 ? round($totalRevenue / $totalSold, 0) : 0,
            'revenue_per_click' => $totalClicks > 0 ? round($totalRevenue / $totalClicks, 0) : 0,
            'best_traffic_total' => (($bestTrafficDay['views'] ?? 0) + ($bestTrafficDay['clicks'] ?? 0)),
            'best_traffic_label' => $bestTrafficDay['date'] ?? '-',
            'best_revenue_amount' => (float) ($bestRevenueDay['sales'] ?? 0),
            'best_revenue_label' => $bestRevenueDay['date'] ?? '-',
            'active_sales_days' => collect($clicksPerDay)->filter(fn ($day) => ($day['sales'] ?? 0) > 0)->count(),
            'active_traffic_days' => collect($clicksPerDay)->filter(fn ($day) => (($day['views'] ?? 0) + ($day['clicks'] ?? 0)) > 0)->count(),
        ];

        $currentSummary = $this->summarizePeriod($currentTrendPeriod);
        $previousSummary = $this->summarizePeriod($previousTrendPeriod);

        $trendStats = [
            'views' => $this->makeTrend($currentSummary['views'], $previousSummary['views'], 'vs 7 hari sebelumnya'),
            'clicks' => $this->makeTrend($currentSummary['clicks'], $previousSummary['clicks'], 'vs 7 hari sebelumnya'),
            'sold' => $this->makeTrend($currentSummary['sold'], $previousSummary['sold'], 'vs 7 hari sebelumnya'),
            'revenue' => $this->makeTrend($currentSummary['revenue'], $previousSummary['revenue'], 'vs 7 hari sebelumnya'),
            'conversion_click_to_sale' => $this->makeTrend(
                $currentSummary['clicks'] > 0 ? round(($currentSummary['sold'] / $currentSummary['clicks']) * 100, 1) : 0,
                $previousSummary['clicks'] > 0 ? round(($previousSummary['sold'] / $previousSummary['clicks']) * 100, 1) : 0,
                '7 hari terakhir'
            ),
            'revenue_per_click' => $this->makeTrend(
                $currentSummary['clicks'] > 0 ? round($currentSummary['revenue'] / $currentSummary['clicks'], 0) : 0,
                $previousSummary['clicks'] > 0 ? round($previousSummary['revenue'] / $previousSummary['clicks'], 0) : 0,
                '7 hari terakhir'
            ),
        ];

        return [
            'isProUser' => $user->isPro(),
            'totalClicks' => $totalClicks,
            'totalProfileViews' => $totalProfileViews,
            'totalSold' => $totalSold,
            'totalRevenue' => $totalRevenue,
            'topProducts' => $topProducts,
            'topViewedProducts' => $topViewedProducts,
            'clicksPerDay' => $clicksPerDay,
            'advancedStats' => $advancedStats,
            'trendStats' => $trendStats,
            'analyticsRangeLabel' => $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y'),
        ];
    }

    private function getClicksPerDay($userId, $startDate, $endDate): array
    {
        $clicksData = ProductViews::whereHas('product', fn ($q) => $q->where('user_id', $userId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as clicks')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('clicks', 'date');

        $viewsData = ProfileView::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('views', 'date');

        $salesData = DB::table('product_sales')
            ->join('products', 'product_sales.product_id', '=', 'products.id')
            ->where('products.user_id', $userId)
            ->whereBetween('product_sales.created_at', [$startDate, $endDate])
            ->selectRaw('DATE(product_sales.created_at) as date, SUM(product_sales.qty * product_sales.price) as revenue, SUM(product_sales.qty) as sold')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $result = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $result[] = [
                'date' => $current->format('d M'),
                'full_date' => $current->format('Y-m-d'),
                'clicks' => (int) ($clicksData[$dateStr] ?? 0),
                'views' => (int) ($viewsData[$dateStr] ?? 0),
                'sales' => (float) data_get($salesData, $dateStr . '.revenue', 0),
                'sold' => (int) data_get($salesData, $dateStr . '.sold', 0),
            ];
            $current->addDay();
        }

        return $result;
    }

    private function exportCsv(array $analytics): StreamedResponse
    {
        $filename = 'analitik-pro-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($analytics) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Ringkasan Analitik Pro']);
            fputcsv($handle, ['Periode', $analytics['analyticsRangeLabel']]);
            fputcsv($handle, ['Total Views Profil', $analytics['totalProfileViews']]);
            fputcsv($handle, ['Total Klik Produk', $analytics['totalClicks']]);
            fputcsv($handle, ['Total Produk Terjual', $analytics['totalSold']]);
            fputcsv($handle, ['Total Pendapatan', $analytics['totalRevenue']]);
            fputcsv($handle, ['Konversi View ke Klik (%)', $analytics['advancedStats']['conversion_view_to_click']]);
            fputcsv($handle, ['Konversi Klik ke Penjualan (%)', $analytics['advancedStats']['conversion_click_to_sale']]);
            fputcsv($handle, ['Rata-rata Nilai per Penjualan', $analytics['advancedStats']['avg_revenue_per_sale']]);
            fputcsv($handle, ['Pendapatan per Klik', $analytics['advancedStats']['revenue_per_click']]);
            fputcsv($handle, []);

            fputcsv($handle, ['Top Produk Berdasarkan Penjualan']);
            fputcsv($handle, ['Produk', 'Terjual', 'Views', 'Pendapatan']);
            foreach ($analytics['topProducts'] as $product) {
                fputcsv($handle, [
                    $product->title,
                    $product->sold ?? 0,
                    $product->views_count ?? 0,
                    $product->revenue ?? 0,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Top Produk Berdasarkan Dilihat']);
            fputcsv($handle, ['Produk', 'Views', 'Terjual', 'Pendapatan']);
            foreach ($analytics['topViewedProducts'] as $product) {
                fputcsv($handle, [
                    $product->title,
                    $product->views_count ?? 0,
                    $product->sold ?? 0,
                    $product->revenue ?? 0,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Performa Harian']);
            fputcsv($handle, ['Tanggal', 'Views Profil', 'Klik Produk', 'Pendapatan']);
            foreach ($analytics['clicksPerDay'] as $day) {
                fputcsv($handle, [
                    $day['full_date'],
                    $day['views'],
                    $day['clicks'],
                    $day['sales'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function exportExcel(array $analytics)
    {
        $filename = 'analitik-pro-' . now()->format('Ymd-His') . '.xls';

        return response()
            ->view('dashboard.analytics.export_excel', $analytics)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function summarizePeriod(array $rows): array
    {
        return [
            'views' => (int) collect($rows)->sum('views'),
            'clicks' => (int) collect($rows)->sum('clicks'),
            'sold' => (int) collect($rows)->sum('sold'),
            'revenue' => (float) collect($rows)->sum('sales'),
        ];
    }

    private function makeTrend(float|int $currentValue, float|int $previousValue, string $context): array
    {
        $delta = $currentValue - $previousValue;
        $direction = $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat');
        $percent = $previousValue > 0
            ? round((abs($delta) / $previousValue) * 100, 1)
            : ($currentValue > 0 ? 100.0 : 0.0);

        return [
            'direction' => $direction,
            'percent' => $percent,
            'delta' => $delta,
            'context' => $context,
            'label' => match ($direction) {
                'up' => 'Naik',
                'down' => 'Turun',
                default => 'Stabil',
            },
        ];
    }
}
