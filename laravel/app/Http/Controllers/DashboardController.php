<?php

namespace App\Http\Controllers;

use App\Models\ProductViews; 
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Link;
use App\Models\Click;
use App\Models\Product;
use Carbon\Carbon;


class DashboardController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $links = Link::where('user_id', $user->id)
            ->withCount('clicks')
            ->get();

       $totalClicks = $user->profile_views ?? 0;

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

        // Aman dari error kalau semua data 0
        $maxClick = !empty($data) ? max($data) : 0;

        // ============================
        // PAYMENT SYSTEM DATA
        // ============================
        $balance = $user->balance ?? 0;
        $totalEarned = $this->paymentService->getTotalEarned($user);

        $recentTransactions = $user->transactions()
            ->latest()
            ->take(5)
            ->get();

        $recentWithdrawals = $user->withdrawals()
            ->latest()
            ->take(5)
            ->get();


        // PRODUCT LIFETIME SALES
        $products = Product::where('user_id', $user->id)
            ->with('images')
            ->withCount('views')
            ->withCount('sales as sold')
            ->withSum('sales as total_qty', 'qty')
            ->get();

        // Total semua views produk user ini
        $totalProductViews = ProductViews::whereHas('product', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        return view('dashboard.index', compact(
            'user',
            'links',
            'totalClicks',
            'totalLinks',
            'activeLinks',
            'labels',
            'data',
            'maxClick',
            'balance',
            'totalEarned',
            'recentTransactions',
            'recentWithdrawals',
            'products',
            'totalProductViews'
        ));
    }

    /**
     * Get dashboard stats via API
     * GET /api/dashboard/stats
     */
    public function getStats()
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'balance' => $user->balance,
            'formatted_balance' => 'Rp ' . number_format($user->balance, 0, ',', '.'),
            'total_earned' => $this->paymentService->getTotalEarned($user),
            'total_clicks' => Click::whereHas('link', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'total_links' => Link::where('user_id', $user->id)->count(),
            'active_links' => Link::where('user_id', $user->id)->where('is_active', true)->count(),
        ]);
    }

}