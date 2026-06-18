@extends('layouts.admin', ['title' => 'Order Details'])

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('orders.index') }}" class="font-bold text-blue-700">Orders</a>
                <span>/</span>
                <span>Order Details</span>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-bold tracking-tight text-slate-950">
                    Order #{{ $order->order_number }}
                </h1>

                <x-admin.status-badge :status="$order->status" />

                <span class="rounded-xl border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-black capitalize text-blue-700">
                    {{ $order->channel }}
                </span>
            </div>

            <p class="mt-2 text-sm text-slate-500">
                {{ $order->ordered_at?->format('M d, Y \a\t h:i A') }} · Order placed from {{ ucfirst($order->channel) }}
            </p>
        </div>

        <div class="flex gap-3">
            <button class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 shadow-sm">
                Print Invoice
            </button>

            <form method="POST" action="{{ route('orders.update-status', $order) }}">
                @csrf
                @method('PATCH')

                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="h-12 rounded-2xl bg-blue-600 px-5 text-sm font-black text-white shadow-lg shadow-blue-600/20"
                >
                    <option value="pending" @selected($order->status === 'pending')>Pending</option>
                    <option value="paid" @selected($order->status === 'paid')>Paid</option>
                    <option value="completed" @selected($order->status === 'completed')>Completed</option>
                    <option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Customer Information</h2>

            <div class="mt-6">
                <p class="text-lg font-black text-slate-950">{{ $order->customer_name }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $order->customer_email }}</p>
            </div>

            <div class="mt-8 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Customer Since</p>
                <p class="mt-2 font-black text-slate-950">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Shipping & Billing Summary</h2>

            <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                <div>
                    <p class="text-sm font-black text-slate-700">Shipping Address</p>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        {{ $order->customer_name }}<br>
                        123 Maple Street<br>
                        Austin, TX 78701<br>
                        United States
                    </p>
                </div>

                <div>
                    <p class="text-sm font-black text-slate-700">Billing Address</p>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Same as shipping address
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Order Totals</h2>

            <div class="mt-6 space-y-4">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Subtotal</span>
                    <span class="font-black text-slate-950">${{ number_format($order->total, 2) }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Shipping</span>
                    <span class="font-black text-slate-950">$0.00</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Tax</span>
                    <span class="font-black text-slate-950">$0.00</span>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <div class="flex justify-between">
                        <span class="font-black text-slate-950">Total</span>
                        <span class="text-2xl font-black text-slate-950">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-[1.4fr_.8fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Order Items ({{ $order->items->count() }})</h2>

            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3">Unit Price</th>
                            <th class="px-4 py-3">Line Total</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 overflow-hidden rounded-2xl bg-slate-100">
                                            @if($item->product?->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" class="h-full w-full object-cover">
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-950">{{ $item->product_name }}</p>
                                            <p class="text-xs text-slate-500">{{ $item->product?->description }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4 font-semibold text-slate-500">{{ $item->product_sku }}</td>
                                <td class="px-4 py-4 font-black text-slate-950">{{ $item->quantity }}</td>
                                <td class="px-4 py-4 font-black text-slate-950">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-4 font-black text-slate-950">${{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="mt-4 text-sm text-slate-500">
                Showing 1 to {{ $order->items->count() }} of {{ $order->items->count() }} items
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Order Activity</h2>

            <div class="mt-6 space-y-6">
                @foreach([
                    ['title' => 'Order Placed', 'desc' => 'Customer placed the order.', 'color' => 'emerald'],
                    ['title' => 'Payment Completed', 'desc' => 'Payment was successfully processed.', 'color' => 'blue'],
                    ['title' => 'Order Confirmed', 'desc' => 'Order has been confirmed.', 'color' => 'violet'],
                    ['title' => 'Prepared', 'desc' => 'Order is ready for processing.', 'color' => 'amber'],
                ] as $activity)
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-50 text-blue-600 ring-1 ring-slate-200">
                            ✓
                        </div>

                        <div>
                            <p class="font-black text-slate-950">{{ $activity['title'] }}</p>
                            <p class="mt-1 text-sm leading-6 text-slate-500">{{ $activity['desc'] }}</p>
                            <p class="mt-1 text-xs font-bold text-slate-400">{{ $order->ordered_at?->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="mt-6 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-black text-blue-700 hover:bg-blue-50">
                View Full Timeline →
            </button>
        </div>
    </div>
</div>
@endsection
