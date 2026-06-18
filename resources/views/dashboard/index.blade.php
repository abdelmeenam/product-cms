@extends('layouts.admin', ['title' => 'Overview'])

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950">Overview</h1>
            <p class="mt-2 text-sm text-slate-500">
                Track catalogue performance across products and orders.
            </p>
        </div>

        <button class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm">
            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1z"/>
            </svg>
            May 12 – May 18, 2025
            <span>⌄</span>
        </button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-5">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M6 6h15l-2 8H8L6 6z"/>
                        <path stroke-width="2" d="M6 6L5 3H2"/>
                        <circle cx="9" cy="20" r="1.5"/>
                        <circle cx="18" cy="20" r="1.5"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500">Total Orders</p>
                    <p class="mt-2 text-2xl font-black text-slate-950">{{ number_format($totalOrders) }}</p>
                    <p class="mt-2 text-xs font-bold text-emerald-600">↗ 18.7% <span class="font-medium text-slate-400">vs previous week</span></p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-5">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M12 8c-2 0-3 1-3 2s1 2 3 2 3 1 3 2-1 2-3 2m0-10v12"/>
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500">Revenue</p>
                    <p class="mt-2 text-2xl font-black text-slate-950">${{ number_format($revenue, 2) }}</p>
                    <p class="mt-2 text-xs font-bold text-emerald-600">↗ 22.4% <span class="font-medium text-slate-400">vs previous week</span></p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-5">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M21 8l-9-5-9 5 9 5 9-5z"/>
                        <path stroke-width="2" d="M3 8v8l9 5 9-5V8"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500">Units Sold</p>
                    <p class="mt-2 text-2xl font-black text-slate-950">{{ number_format($unitsSold) }}</p>
                    <p class="mt-2 text-xs font-bold text-emerald-600">↗ 16.3% <span class="font-medium text-slate-400">vs previous week</span></p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-5">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M20 13V7a2 2 0 0 0-2-2h-6L4 13l7 7 9-7z"/>
                        <circle cx="15" cy="9" r="1"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500">Average Order Value</p>
                    <p class="mt-2 text-2xl font-black text-slate-950">${{ number_format($averageOrderValue, 2) }}</p>
                    <p class="mt-2 text-xs font-bold text-emerald-600">↗ 3.1% <span class="font-medium text-slate-400">vs previous week</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid gap-5 xl:grid-cols-[1.35fr_1fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-950">Sales Performance</h2>
                <span class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-500">
                    Last 30 days
                </span>
            </div>
            <canvas id="salesPerformanceChart" height="115"></canvas>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-6 text-lg font-black text-slate-950">Orders by Channel</h2>

            <div class="grid items-center gap-6 md:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                <canvas id="ordersByChannelChart" height="180"></canvas>

                <div class="space-y-4">
                    @foreach($ordersByChannel as $channel)
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full
                                    {{ $loop->index === 0 ? 'bg-blue-600' : '' }}
                                    {{ $loop->index === 1 ? 'bg-emerald-500' : '' }}
                                    {{ $loop->index === 2 ? 'bg-amber-500' : '' }}
                                    {{ $loop->index >= 3 ? 'bg-violet-500' : '' }}
                                "></span>
                                <span class="text-sm font-bold capitalize text-slate-600">{{ $channel->channel }}</span>
                            </div>
                            <span class="text-sm font-black text-slate-950">{{ number_format($channel->total) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Tables --}}
    <div class="grid gap-5 xl:grid-cols-[1.35fr_1fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-950">Top Products</h2>
                <a href="{{ route('products.index') }}" class="text-sm font-bold text-blue-700 hover:text-blue-800">
                    View all products
                </a>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-100">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Units Sold</th>
                            <th class="px-4 py-3">Revenue</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($topProducts as $item)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 overflow-hidden rounded-2xl bg-slate-100">
                                            @if($item->product?->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" class="h-full w-full object-cover">
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-950">{{ $item->product_name }}</p>
                                            <p class="line-clamp-1 text-xs text-slate-500">{{ $item->product?->description }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 font-medium text-slate-500">{{ $item->product_sku }}</td>
                                <td class="px-4 py-4 font-bold text-slate-950">{{ number_format($item->units_sold) }}</td>
                                <td class="px-4 py-4 font-bold text-slate-950">${{ number_format($item->revenue, 2) }}</td>
                                <td class="px-4 py-4">
                                    <x-admin.status-badge :status="$item->product?->status ?? 'active'" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-950">Recent Orders</h2>
                <a href="{{ route('orders.index') }}" class="text-sm font-bold text-blue-700 hover:text-blue-800">
                    View all orders
                </a>
            </div>

            <div class="space-y-3">
                @foreach($recentOrders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between gap-4 rounded-2xl border border-slate-100 p-4 transition hover:border-blue-200 hover:bg-blue-50/40">
                        <div>
                            <p class="font-black text-slate-950">#{{ $order->order_number }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $order->ordered_at?->format('M d, Y h:i A') }}</p>
                        </div>

                        <div class="text-right">
                            <p class="font-black text-slate-950">${{ number_format($order->total, 2) }}</p>
                            <p class="mt-1 text-xs font-bold capitalize text-slate-500">{{ $order->channel }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    const salesLabels = @json($salesPerformance->pluck('date'));
    const salesData = @json($salesPerformance->pluck('revenue'));

    new Chart(document.getElementById('salesPerformanceChart'), {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Revenue',
                data: salesData,
                tension: 0.4,
                fill: true,
                borderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    ticks: {
                        callback: value => '$' + value
                    }
                }
            }
        }
    });

    const channelLabels = @json($ordersByChannel->pluck('channel'));
    const channelData = @json($ordersByChannel->pluck('total'));

    new Chart(document.getElementById('ordersByChannelChart'), {
        type: 'doughnut',
        data: {
            labels: channelLabels,
            datasets: [{
                data: channelData,
                borderWidth: 0,
            }]
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
