<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $completedStatuses = ['paid', 'completed'];

        $totalOrders = Order::query()->count();
        $revenue = (float) Order::query()
            ->whereIn('status', $completedStatuses)
            ->sum('total');
        $fulfilledOrdersCount = Order::query()
            ->whereIn('status', $completedStatuses)
            ->count();
        $unitsSold = OrderItem::query()->sum('quantity');
        $averageOrderValue = $fulfilledOrdersCount > 0
            ? round($revenue / $fulfilledOrdersCount, 2)
            : 0.0;

        $totalProducts = Product::query()->count();
        $lowStockProducts = Product::query()
            ->whereBetween('stock', [1, 10])
            ->count();
        $outOfStockProducts = Product::query()
            ->where('stock', 0)
            ->count();

        $ordersByChannel = Order::query()
            ->select('channel')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('channel')
            ->orderByDesc('total')
            ->get();

        $topProducts = OrderItem::query()
            ->select('product_id', 'product_name', 'product_sku')
            ->selectRaw('SUM(quantity) as units_sold')
            ->selectRaw('SUM(line_total) as revenue')
            ->with('product:id,name,sku,image,status')
            ->groupBy('product_id', 'product_name', 'product_sku')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        $recentOrders = Order::query()
            ->withCount('items')
            ->latest('ordered_at')
            ->latest('id')
            ->limit(5)
            ->get();

        /** @var Collection<int, array{label: string, value: float}> $salesPerformance */
        $salesPerformance = Order::query()
            ->selectRaw('DATE(ordered_at) as date')
            ->selectRaw('SUM(total) as revenue')
            ->whereNotNull('ordered_at')
            ->whereIn('status', $completedStatuses)
            ->groupBy(DB::raw('DATE(ordered_at)'))
            ->orderByDesc('date')
            ->limit(30)
            ->get()
            ->sortBy('date')
            ->values()
            ->map(static fn (object $sale): array => [
                'label' => Carbon::parse($sale->date)->format('M d'),
                'value' => (float) $sale->revenue,
            ]);

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
