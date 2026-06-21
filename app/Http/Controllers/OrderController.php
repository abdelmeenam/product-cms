<?php

namespace App\Http\Controllers;

use App\enums\OrderChannel;
use App\enums\OrderStatus;
use App\Http\Requests\IndexOrderRequest;
use App\Models\Order;
use Illuminate\View\View;
use Illuminate\Http\Response;


class OrderController extends Controller
{
    public function index(IndexOrderRequest $request): View|Response
    {
        $orders = Order::query()
            ->withCount('items')
            ->filterIndex($request->filters())
            ->orderByRaw('COALESCE(ordered_at, created_at) DESC')
            ->orderByDesc('id')
            ->paginate($request->perPage())
            ->withQueryString();

        if ($request->ajax()) {
            return response()->view('orders.partials.table', [
                'orders' => $orders,
            ]);
        }

        return view('orders.index', [
            'orderChannelOptions' => OrderChannel::options(),
            'orderMetricCounts' => $this->orderMetricCounts(),
            'orderStatusOptions' => OrderStatus::options(),
            'orders' => $orders,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load([
            'items.product:id,name,sku,image,description,status',
        ]);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * @return array{total: int, pending: int, fulfilled: int, cancelled: int}
     */
    private function orderMetricCounts(): array
    {
        $fulfilledStatuses = OrderStatus::fulfilledValues();

        $countsQuery = Order::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending',
                [OrderStatus::Pending->value]
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled',
                [OrderStatus::Cancelled->value]
            );

        if ($fulfilledStatuses === []) {
            $countsQuery->selectRaw('0 as fulfilled');
        } else {
            $fulfilledPlaceholders = implode(', ', array_fill(0, count($fulfilledStatuses), '?'));

            $countsQuery->selectRaw(
                "SUM(CASE WHEN status IN ({$fulfilledPlaceholders}) THEN 1 ELSE 0 END) as fulfilled",
                $fulfilledStatuses
            );
        }

        $counts = $countsQuery->first();

        return [
            'total' => (int) $counts->total,
            'pending' => (int) $counts->pending,
            'fulfilled' => (int) $counts->fulfilled,
            'cancelled' => (int) $counts->cancelled,
        ];
    }
}
