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
                @forelse($orders as $order)
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
                            <span class="font-bold text-slate-700">{{ $order->channel->label() }}</span>
                        </td>

                        <td class="px-6 py-5 font-black text-slate-950">
                            {{ $order->items_count }}
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
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="mx-auto max-w-sm">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl font-black text-blue-600">
                                    #
                                </div>

                                <h3 class="mt-4 text-lg font-black text-slate-950">
                                    No orders found
                                </h3>

                                <p class="mt-2 text-sm text-slate-500">
                                    Try adjusting the filters or search terms.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex flex-col gap-4 border-t border-slate-100 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
        <div class="text-sm font-medium text-slate-500">
            Showing
            <span class="font-black text-slate-950">{{ $orders->firstItem() ?? 0 }}</span>
            to
            <span class="font-black text-slate-950">{{ $orders->lastItem() ?? 0 }}</span>
            of
            <span class="font-black text-slate-950">{{ $orders->total() }}</span>
            orders
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-slate-500">Rows per page</span>

                <select
                    x-model="filters.per_page"
                    @change="changePerPage($event.target.value)"
                    class="h-11 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-black text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
                    <option value="8">8</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                @if($orders->onFirstPage())
                    <span class="inline-flex h-11 w-11 cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-300">
                        &lsaquo;
                    </span>
                @else
                    <a
                        href="{{ $orders->previousPageUrl() }}"
                        data-pagination-link
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                    >
                        &lsaquo;
                    </a>
                @endif

                @php
                    $currentPage = $orders->currentPage();
                    $lastPage = $orders->lastPage();

                    $start = max(1, $currentPage - 2);
                    $end = min($lastPage, $currentPage + 2);

                    if ($currentPage <= 3) {
                        $start = 1;
                        $end = min($lastPage, 5);
                    }

                    if ($currentPage >= $lastPage - 2) {
                        $start = max(1, $lastPage - 4);
                        $end = $lastPage;
                    }
                @endphp

                @if($start > 1)
                    <a
                        href="{{ $orders->url(1) }}"
                        data-pagination-link
                        class="inline-flex h-11 min-w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-black text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                    >
                        1
                    </a>

                    @if($start > 2)
                        <span class="inline-flex h-11 min-w-11 items-center justify-center px-2 text-sm font-black text-slate-400">
                            ...
                        </span>
                    @endif
                @endif

                @for($page = $start; $page <= $end; $page++)
                    @if($page === $currentPage)
                        <span class="inline-flex h-11 min-w-11 items-center justify-center rounded-2xl bg-blue-600 px-4 text-sm font-black text-white shadow-lg shadow-blue-600/20">
                            {{ $page }}
                        </span>
                    @else
                        <a
                            href="{{ $orders->url($page) }}"
                            data-pagination-link
                            class="inline-flex h-11 min-w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-black text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                @if($end < $lastPage)
                    @if($end < $lastPage - 1)
                        <span class="inline-flex h-11 min-w-11 items-center justify-center px-2 text-sm font-black text-slate-400">
                            ...
                        </span>
                    @endif

                    <a
                        href="{{ $orders->url($lastPage) }}"
                        data-pagination-link
                        class="inline-flex h-11 min-w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-black text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                    >
                        {{ $lastPage }}
                    </a>
                @endif

                @if($orders->hasMorePages())
                    <a
                        href="{{ $orders->nextPageUrl() }}"
                        data-pagination-link
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                    >
                        &rsaquo;
                    </a>
                @else
                    <span class="inline-flex h-11 w-11 cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-300">
                        &rsaquo;
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
