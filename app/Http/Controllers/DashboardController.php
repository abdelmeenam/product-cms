<?php

namespace App\Http\Controllers;

use App\enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $fulfilledStatuses = OrderStatus::fulfilledValues();

        $totalOrders = Order::query()->count();

        $fulfilledOrdersCount = Order::query()
            ->whereIn('status', $fulfilledStatuses)
            ->count();

        $revenue = (float) Order::query()
            ->whereIn('status', $fulfilledStatuses)
            ->sum('total');

        $unitsSold = (int) OrderItem::query()
            ->whereHas('order', function ($query) use ($fulfilledStatuses): void {
                $query->whereIn('status', $fulfilledStatuses);
            })
            ->sum('quantity');

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
            ->with('product:id,name,sku,image,status,description')
            ->whereHas('order', function ($query) use ($fulfilledStatuses): void {
                $query->whereIn('status', $fulfilledStatuses);
            })
            ->groupBy('product_id', 'product_name', 'product_sku')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        $recentOrders = Order::query()
            ->orderByDesc(DB::raw('COALESCE(ordered_at, created_at)'))
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $salesPerformance = Order::query()
            ->selectRaw('DATE(COALESCE(ordered_at, created_at)) as date')
            ->selectRaw('SUM(total) as revenue')
            ->whereIn('status', $fulfilledStatuses)
            ->groupByRaw('DATE(COALESCE(ordered_at, created_at))')
            ->orderByRaw('DATE(COALESCE(ordered_at, created_at)) ASC')
            ->get()
            ->map(static fn ($sale): array => [
                'label' => Carbon::parse($sale->date)->format('M d'),
                'value' => (float) $sale->revenue,
            ])
            ->values();

        return view('dashboard.index', compact(
            'fulfilledOrdersCount',
            'ordersByChannel',
            'recentOrders',
            'revenue',
            'salesPerformance',
            'topProducts',
            'totalOrders',
            'unitsSold',
        ));
    }
}
