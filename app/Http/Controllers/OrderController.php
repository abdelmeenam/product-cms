<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $status = $request->string('status')->trim()->value();
        $channel = $request->string('channel')->trim()->value();
        $dateFrom = $request->string('date_from')->trim()->value();
        $dateTo = $request->string('date_to')->trim()->value();

        $orders = Order::query()
            ->withCount('items')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $nestedQuery) use ($search): void {
                    $nestedQuery
                        ->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn (Builder $query): Builder => $query->where('status', $status))
            ->when($channel !== '', fn (Builder $query): Builder => $query->where('channel', $channel))
            ->when($dateFrom !== '', fn (Builder $query): Builder => $query->whereDate('ordered_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn (Builder $query): Builder => $query->whereDate('ordered_at', '<=', $dateTo))
            ->latest('ordered_at')
            ->latest('id')
            ->paginate(8)
            ->withQueryString();

        $totalOrders = Order::query()->count();
        $pendingOrders = Order::query()
            ->where('status', 'pending')
            ->count();
        $completedOrders = Order::query()
            ->whereIn('status', ['paid', 'completed'])
            ->count();
        $cancelledOrders = Order::query()
            ->where('status', 'cancelled')
            ->count();

        return view('orders.index', compact(
            'cancelledOrders',
            'completedOrders',
            'orders',
            'pendingOrders',
            'totalOrders',
        ));
    }

    public function show(Order $order): View
    {
        $order->load('items.product');

        return view('orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $order->update($request->validated());

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }
}
