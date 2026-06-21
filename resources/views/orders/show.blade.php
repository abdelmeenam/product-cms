@extends('layouts.admin', ['title' => 'Order Details'])

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('orders.index') }}" class="font-bold text-blue-700 hover:text-blue-800">Orders</a>
                <span>/</span>
                <span>Order Details</span>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-black tracking-tight text-slate-950">
                    Order #{{ $order->order_number }}
                </h1>

                <x-admin.status-badge :status="$orderStatusValue" />

                <span class="inline-flex items-center gap-2 rounded-2xl border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">
                    <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                    {{ $orderChannelLabel }}
                </span>
            </div>

            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                Review the order snapshot, purchased items, customer contact, and performance impact.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">

            <a
                href="{{ route('orders.index') }}"
                class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
            >
                Back to Orders
            </a>
        </div>
    </div>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
        <div class="grid gap-6 xl:grid-cols-[1.05fr_.95fr]">
            <div>
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M6 6h15l-2 8H8L6 6z"/>
                        <path stroke-width="2" d="M6 6L5 3H2"/>
                        <circle cx="9" cy="20" r="1.5"/>
                        <circle cx="18" cy="20" r="1.5"/>
                    </svg>
                </div>

                <p class="mt-5 text-sm font-black uppercase tracking-[0.18em] text-slate-400">
                    Order Snapshot
                </p>

                <h2 class="mt-3 text-4xl font-black tracking-tight text-slate-950">
                    ${{ number_format((float) $order->total, 2) }}
                </h2>

                <p class="mt-3 max-w-xl text-sm leading-6 text-slate-500">
                    {{ $orderInsights['status_note'] }}
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($orderMetrics as $metric)
                    @php
                        $toneClasses = match ($metric['tone']) {
                            'blue' => 'bg-blue-50 text-blue-700 ring-blue-100',
                            'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                            'violet' => 'bg-violet-50 text-violet-700 ring-violet-100',
                            default => 'bg-slate-50 text-slate-700 ring-slate-100',
                        };
                    @endphp

                    <div class="rounded-3xl p-5 ring-1 {{ $toneClasses }}">
                        <p class="text-xs font-black uppercase tracking-[0.16em] opacity-70">
                            {{ $metric['label'] }}
                        </p>

                        <p class="mt-3 text-xl font-black">
                            {{ $metric['value'] }}
                        </p>

                        <p class="mt-1 text-xs font-semibold opacity-70">
                            {{ $metric['helper'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[1.4fr_.8fr]">
        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-black text-slate-950">Purchased Items</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Each row reflects the product snapshot stored on the order.
                    </p>
                </div>

                <span class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-black text-slate-700">
                    {{ number_format($order->items->count()) }} items
                </span>
            </div>

            <div class="mt-6 overflow-hidden rounded-3xl border border-slate-100">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-5 py-4">Product</th>
                            <th class="px-5 py-4">SKU</th>
                            <th class="px-5 py-4">Qty</th>
                            <th class="px-5 py-4">Unit Price</th>
                            <th class="px-5 py-4 text-right">Line Total</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($order->items as $item)
                            <tr class="transition hover:bg-blue-50/30">
                                <td class="px-5 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 text-slate-300">
                                            @if($item->product?->image)
                                                <img
                                                    src="{{ asset('storage/' . $item->product->image) }}"
                                                    alt="{{ $item->product_name }}"
                                                    class="h-full w-full object-cover"
                                                >
                                            @else
                                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-width="2" d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14m18 0H3m18 0l-6-8-4 5-3-4-5 7"/>
                                                </svg>
                                            @endif
                                        </div>

                                        <div>
                                            <p class="font-black text-slate-950">
                                                {{ $item->product_name }}
                                            </p>

                                            <p class="mt-1 line-clamp-1 text-xs text-slate-500">
                                                {{ $item->product?->description ?? 'No product description available.' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-5 font-semibold text-slate-500">
                                    {{ $item->product_sku }}
                                </td>

                                <td class="px-5 py-5 font-black text-slate-950">
                                    {{ number_format($item->quantity) }}
                                </td>

                                <td class="px-5 py-5 font-black text-slate-700">
                                    ${{ number_format((float) $item->unit_price, 2) }}
                                </td>

                                <td class="px-5 py-5 text-right font-black text-slate-950">
                                    ${{ number_format((float) $item->line_total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <p class="font-black text-slate-950">No items found</p>
                                    <p class="mt-2 text-sm text-slate-500">
                                        This order does not have any recorded items.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="space-y-6">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                            <path stroke-width="2" d="M4 21a8 8 0 0 1 16 0"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-xl font-black text-slate-950">Customer</h2>
                        <p class="text-sm text-slate-500">Order contact snapshot</p>
                    </div>
                </div>

                <div class="mt-6 rounded-3xl bg-slate-50 p-5">
                    <p class="text-lg font-black text-slate-950">
                        {{ $order->customer_name }}
                    </p>

                    <p class="mt-2 text-sm text-slate-500">
                        {{ $customerEmail }}
                    </p>
                </div>

                <div class="mt-5 grid gap-3">
                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 px-4 py-3">
                        <span class="text-sm font-semibold text-slate-500">Channel</span>
                        <span class="text-sm font-black text-slate-950">{{ $orderChannelLabel }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 px-4 py-3">
                        <span class="text-sm font-semibold text-slate-500">Status</span>
                        <span class="text-sm font-black text-slate-950">{{ $orderStatusLabel }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 px-4 py-3">
                        <span class="text-sm font-semibold text-slate-500">Recorded</span>
                        <span class="text-sm font-black text-slate-950">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-slate-950">Financial Summary</h2>

                    <span class="rounded-2xl bg-emerald-50 px-4 py-2 text-sm font-black text-emerald-700">
                        Total
                    </span>
                </div>

                <div class="mt-6 space-y-4">
                    @foreach($orderFinancialRows as $row)
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="font-semibold text-slate-500">
                                {{ $row['label'] }}
                            </span>

                            <span class="text-base font-black text-slate-950">
                                {{ $row['value'] }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 rounded-3xl bg-blue-50 p-5">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-blue-500">
                        Top Value Item
                    </p>

                    <p class="mt-2 font-black text-blue-950">
                        {{ $orderInsights['top_item'] }}
                    </p>

                    <p class="mt-1 text-sm font-semibold text-blue-700">
                        Avg. unit price: {{ $orderInsights['average_unit_price'] }}
                    </p>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
