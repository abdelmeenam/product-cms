<?php

namespace App\Http\Controllers;

use App\enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $completedStatuses = OrderStatus::fulfilledValues();

        /*
        |--------------------------------------------------------------------------
        | KPI Cards
        |--------------------------------------------------------------------------
        */

        $totalOrders = Order::query()->count();

        $revenue = (float) Order::query()
            ->whereIn('status', $completedStatuses)
            ->sum('total');

        $fulfilledOrdersCount = Order::query()
            ->whereIn('status', $completedStatuses)
            ->count();

        $unitsSold = (int) OrderItem::query()
            ->sum('quantity');

        $averageOrderValue = $fulfilledOrdersCount > 0
            ? round($revenue / $fulfilledOrdersCount, 2)
            : 0.0;

        /*
        |--------------------------------------------------------------------------
        | Product Health
        |--------------------------------------------------------------------------
        */

        $totalProducts = Product::query()->count();

        $lowStockProducts = Product::query()
            ->whereBetween('stock', [1, 10])
            ->count();

        $outOfStockProducts = Product::query()
            ->where('stock', 0)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Orders by Channel
        |--------------------------------------------------------------------------
        */

        $ordersByChannel = Order::query()
            ->select('channel')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('channel')
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Top Products
        |--------------------------------------------------------------------------
        */

        $topProducts = OrderItem::query()
            ->select('product_id', 'product_name', 'product_sku')
            ->selectRaw('SUM(quantity) as units_sold')
            ->selectRaw('SUM(line_total) as revenue')
            ->with('product:id,name,sku,image,status,description')
            ->groupBy('product_id', 'product_name', 'product_sku')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Orders
        |--------------------------------------------------------------------------
        */

        $recentOrders = Order::query()
            ->withCount('items')
            ->orderByDesc(DB::raw('COALESCE(ordered_at, created_at)'))
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Sales Performance Chart
        |--------------------------------------------------------------------------
        | Important:
        | ordered_at may be null in seeded/demo data.
        | So we fallback to created_at to avoid an empty chart.
        */

        $salesPerformance = Order::query()
            ->selectRaw('DATE(COALESCE(ordered_at, created_at)) as date')
            ->selectRaw('SUM(total) as revenue')
            ->whereIn('status', $completedStatuses)
            ->groupByRaw('DATE(COALESCE(ordered_at, created_at))')
            ->orderByRaw('DATE(COALESCE(ordered_at, created_at)) ASC')
            ->get()
            ->map(static function ($sale): array {
                return [
                    'label' => Carbon::parse($sale->date)->format('M d'),
                    'value' => (float) $sale->revenue,
                ];
            })
            ->values();

        return view('dashboard.index', compact(
            'averageOrderValue',
            'lowStockProducts',
            'ordersByChannel',
            'outOfStockProducts',
            'recentOrders',
            'revenue',
            'salesPerformance',
            'topProducts',
            'totalOrders',
            'totalProducts',
            'unitsSold',
        ));
    }
}
