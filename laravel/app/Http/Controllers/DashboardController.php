<?php

namespace App\Http\Controllers;

use App\Models\ProductViews;
use App\Models\ProfileView;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $links       = Link::where('user_id', $user->id)->withCount('click')->get();
        $totalLinks  = $links->count();
        $activeLinks = $links->where('is_active', 1)->count();

        // Chart data (7 hari terakhir)
        $from = Carbon::now()->subDays(6)->startOfDay();
        $to   = Carbon::now()->endOfDay();

        // ✅ VIEWS = berapa kali halaman profil publik dikunjungi (dari tabel profile_views)
        $profileViewsRaw = ProfileView::where('user_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        // ✅ CLICKS = berapa kali produk diklik di halaman publik (dari tabel product_views)
        $productClicksRaw = ProductViews::whereHas('product', fn($q) => $q->where('user_id', $user->id))
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels     = [];
        $data       = [];   // views (kuning)
        $clicksData = [];   // klik produk (biru)
        $maxClick   = 0;

        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $dateKey      = $d->format('Y-m-d');
            $labels[]     = $d->format('d M');
            $data[]       = $profileViewsRaw[$dateKey]  ?? 0;
            $clicksData[] = $productClicksRaw[$dateKey] ?? 0;
            $maxClick     = max($maxClick, end($data), end($clicksData));
        }



        // Payment
        $balance            = $user->balance ?? 0;
        $totalEarned        = $this->paymentService->getTotalEarned($user);
        $recentTransactions = $user->transactions()->latest()->take(5)->get();
        $recentWithdrawals  = $user->withdrawals()->latest()->take(5)->get();

        // Products
        $products = Product::where('user_id', $user->id)
            ->with('images')
            ->withCount('views')
            ->withCount('sales as sold')
            ->withSum('sales as total_qty', 'qty')
            ->get();



        return view('dashboard.index', compact(
            'user',
            'links',
            'totalLinks',
            'activeLinks',
            'labels',
            'data',
            'clicksData',
            'maxClick',
            'balance',
            'totalEarned',
            'recentTransactions',
            'recentWithdrawals',
            'products',
        ));
    }

    public function chartData(Request $request)
    {
        $days  = $request->input('days', 7);
        $start = $request->input('start');
        $end   = $request->input('end');

        if ($start && $end) {
            $from = Carbon::parse($start)->startOfDay();
            $to   = Carbon::parse($end)->endOfDay();
        } else {
            $from = Carbon::now()->subDays((int)$days - 1)->startOfDay();
            $to   = Carbon::now()->endOfDay();
        }

        $userId = Auth::id();

        // ✅ Views = kunjungan halaman profil
        $views = ProfileView::where('user_id', $userId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        // ✅ Clicks = klik produk
        $clicks = ProductViews::whereHas('product', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels     = [];
        $viewsData  = [];
        $clicksData = [];

        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $dateKey      = $d->format('Y-m-d');
            $labels[]     = $d->format('d M');
            $viewsData[]  = $views[$dateKey]  ?? 0;
            $clicksData[] = $clicks[$dateKey] ?? 0;
        }

        return response()->json([
            'labels'       => $labels,
            'views'        => $viewsData,
            'clicks'       => $clicksData,
            'total_views'  => array_sum($viewsData),
            'total_clicks' => array_sum($clicksData),
        ]);
    }

    public function getStats()
    {
        $user = Auth::user();

        return response()->json([
            'success'           => true,
            'user'              => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'balance'           => $user->balance,
            'formatted_balance' => 'Rp ' . number_format($user->balance, 0, ',', '.'),
            'total_earned'      => $this->paymentService->getTotalEarned($user),
            'total_clicks'      => Click::whereHas('link', fn($q) => $q->where('user_id', $user->id))->count(),
            'total_links'       => Link::where('user_id', $user->id)->count(),
            'active_links'      => Link::where('user_id', $user->id)->where('is_active', true)->count(),
        ]);
    }
}