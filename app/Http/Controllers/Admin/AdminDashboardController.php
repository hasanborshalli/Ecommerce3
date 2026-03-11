<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalRevenue  = (float) Order::where('status', '!=', 'cancelled')->sum('total');
        $totalProfit   = (float) DB::table('orders')->where('status', '!=', 'cancelled')->selectRaw('SUM(`total` - cost_total) as p')->value('p');
        $totalOrders   = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalProducts = Product::where('is_active', true)->count();
        $lowStockCount = Product::lowStock()->count();

        $totalCustomers        = Customer::count();
        $newCustomersThisMonth = Customer::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $pendingReviews       = Review::where('status', 'pending')->count();
        $latestPendingReviews = Review::where('status', 'pending')->latest()->take(4)->get();

        $thisMonthRevenue = (float) Order::where('status', '!=', 'cancelled')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');
        $lastMonthRevenue = (float) Order::where('status', '!=', 'cancelled')->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->sum('total');
        $revenueGrowth    = $lastMonthRevenue > 0 ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : null;

        $recentOrders    = Order::with('items')->orderBy('created_at', 'desc')->take(8)->get();
        $lowStockProducts = Product::lowStock()->with('category')->orderBy('stock', 'asc')->take(6)->get();

        $salesChart = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'), DB::raw('SUM(`total` - cost_total) as profit'))
            ->groupBy('date')->orderBy('date')->get()->keyBy('date');

        $chartDates = $chartRevenue = $chartProfit = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $row = $salesChart->get($day);
            $chartDates[]   = now()->subDays($i)->format('M d');
            $chartRevenue[] = $row ? round((float) $row->revenue, 2) : 0;
            $chartProfit[]  = $row ? round((float) $row->profit, 2) : 0;
        }

        $topProducts = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as units_sold'), DB::raw('SUM(line_total) as revenue'), DB::raw('SUM(line_profit) as profit'))
            ->whereHas('order', fn($q) => $q->where('status', '!=', 'cancelled'))
            ->groupBy('product_id', 'product_name')->orderByDesc('revenue')->take(5)->get();

        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')->toArray();

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalProfit', 'totalOrders', 'pendingOrders',
            'totalProducts', 'lowStockCount',
            'totalCustomers', 'newCustomersThisMonth',
            'pendingReviews', 'latestPendingReviews',
            'revenueGrowth',
            'recentOrders', 'lowStockProducts',
            'chartDates', 'chartRevenue', 'chartProfit',
            'topProducts', 'ordersByStatus',
        ));
    }
}
