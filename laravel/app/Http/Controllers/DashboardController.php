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
use App\Models\DigitalOrder;
use App\Models\PhysicalOrder;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

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

        // ✅ Total Produk & Total Pesanan
        $totalProducts = Product::where('user_id', $user->id)->count();
        $totalDigitalOrders = DigitalOrder::whereIn(
            'digital_product_id',
            Product::where('user_id', $user->id)->pluck('id')
        )->count();

        $totalPhysicalOrders = PhysicalOrder::where('seller_id', $user->id)->count();

        $totalOrders = $totalDigitalOrders + $totalPhysicalOrders;
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
            'totalProducts',
            'totalOrders',
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

    public function toggleProTrial(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isPro = $user->isPro();

        $user->forceFill([
            'subscription_plan' => $isPro ? 'free' : 'pro',
        ])->save();

        if ($isPro) {
            $profile = $user->fresh()->userProfile;

            if ($profile) {
                $freeAppearance = $user->fresh()->appearanceAccess();

                $profile->update([
                    'bg_type' => in_array($profile->bg_type ?? 'color', $freeAppearance['background_types'], true) ? $profile->bg_type : 'color',
                    'bg_image' => in_array($profile->bg_type ?? 'color', $freeAppearance['background_types'], true) ? $profile->bg_image : null,
                    'btn_style' => in_array($profile->btn_style ?? 'fill', $freeAppearance['button_styles'], true) ? $profile->btn_style : 'fill',
                    'font_family' => in_array($profile->font_family ?? 'Plus Jakarta Sans', $freeAppearance['fonts'], true) ? $profile->font_family : 'Plus Jakarta Sans',
                    'block_layout' => in_array($profile->block_layout ?? 'default', $freeAppearance['block_layouts'], true) ? $profile->block_layout : 'default',
                ]);
            }
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                $isPro
                    ? 'Mode Pro trial dimatikan. Akun kembali ke status Free.'
                    : 'Mode Pro trial berhasil diaktifkan. Akun sekarang berstatus Pro.'
            );
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

    /**
     * ===== STORAGE API =====
     * Get user storage information
     * 
     * Response:
     * - used: bytes yang sudah digunakan
     * - used_formatted: format readable (KB, MB, GB)
     * - limit: bytes limit sebagai total penyimpanan
     * - limit_formatted: format readable
     * - available: bytes yang masih tersedia
     * - available_formatted: format readable
     * - percentage: persentase penggunaan storage
     * - plan: subscription plan user (Free/Pro)
     */
    public function getStorageInfo()
    {
        $user = Auth::user();
        $storageInfo = $user->getStorageInfo();

        return response()->json([
            'success' => true,
            'storage' => $storageInfo,
        ]);
    }
}
