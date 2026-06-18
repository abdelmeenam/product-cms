@extends('layouts.admin', ['title' => 'Orders'])

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-950">Orders</h1>
        <p class="mt-2 text-sm text-slate-500">
            View incoming orders from all channels.
        </p>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Total Orders', 'value' => $totalOrders, 'color' => 'blue'],
            ['label' => 'Pending', 'value' => $pendingOrders, 'color' => 'amber'],
            ['label' => 'Completed', 'value' => $completedOrders, 'color' => 'emerald'],
            ['label' => 'Cancelled', 'value' => $cancelledOrders, 'color' => 'rose'],
        ] as $card)
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($card['value']) }}</p>
                <p class="mt-3 text-xs font-bold text-emerald-600">↗ 8.4% <span class="font-medium text-slate-400">vs previous week</span></p>
            </div>
        @endforeach
    </div>

    <form
        method="GET"
        action="{{ route('orders.index') }}"
        x-data="{
            timeout: null,
            submitForm() {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => this.$refs.form.submit(), 450);
            }
        }"
        x-ref="form"
        class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
    >
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[1.4fr_.8fr_.8fr_.8fr_.8fr_auto]">
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                @input="submitForm"
                placeholder="Search by order or customer..."
                class="h-12 rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-blue-500"
            >

            <select name="status" @change="$refs.form.submit()" class="h-12 rounded-2xl border border-slate-200 px-4 text-sm font-bold text-slate-600">
                <option value="">All Statuses</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
            </select>

            <select name="channel" @change="$refs.form.submit()" class="h-12 rounded-2xl border border-slate-200 px-4 text-sm font-bold text-slate-600">
                <option value="">All Channels</option>
                <option value="website" @selected(request('channel') === 'website')>Website</option>
                <option value="shopify" @selected(request('channel') === 'shopify')>Shopify</option>
                <option value="amazon" @selected(request('channel') === 'amazon')>Amazon</option>
                <option value="manual" @selected(request('channel') === 'manual')>Manual</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" @change="$refs.form.submit()" class="h-12 rounded-2xl border border-slate-200 px-4 text-sm font-bold text-slate-600">

            <input type="date" name="date_to" value="{{ request('date_to') }}" @change="$refs.form.submit()" class="h-12 rounded-2xl border border-slate-200 px-4 text-sm font-bold text-slate-600">

            <a href="{{ route('orders.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 px-4 text-sm font-black text-slate-600">
                Reset
            </a>
        </div>
    </form>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Order</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Channel</th>
                        <th class="px-6 py-4">Items</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Ordered At</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach($orders as $order)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-6 py-5">
                                <a href="{{ route('orders.show', $order) }}" class="font-black text-blue-700 hover:text-blue-800">
                                    #{{ $order->order_number }}
                                </a>
                                <p class="mt-1 text-xs text-slate-500">{{ $order->ordered_at?->format('M d, Y h:i A') }}</p>
                            </td>

                            <td class="px-6 py-5">
                                <p class="font-black text-slate-950">{{ $order->customer_name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $order->customer_email }}</p>
                            </td>

                            <td class="px-6 py-5">
                                <span class="font-bold capitalize text-slate-700">{{ $order->channel }}</span>
                            </td>

                            <td class="px-6 py-5 font-black text-slate-950">
                                {{ $order->items_count ?? $order->items()->count() }}
                            </td>

                            <td class="px-6 py-5 font-black text-slate-950">
                                ${{ number_format($order->total, 2) }}
                            </td>

                            <td class="px-6 py-5">
                                <x-admin.status-badge :status="$order->status" />
                            </td>

                            <td class="px-6 py-5 text-slate-500">
                                <div>{{ $order->ordered_at?->format('M d, Y') }}</div>
                                <div class="text-xs">{{ $order->ordered_at?->format('h:i A') }}</div>
                            </td>

                            <td class="px-6 py-5 text-right">
                                <a href="{{ route('orders.show', $order) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-6 py-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
