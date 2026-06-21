@extends('layouts.admin', ['title' => 'Orders'])

@section('content')
@php
    $orderMetricCards = [
        [
            'key' => 'total',
            'label' => 'Total Orders',
        ],
        [
            'key' => 'pending',
            'label' => 'Pending',
        ],
        [
            'key' => 'fulfilled',
            'label' => 'Fulfilled',
        ],
        [
            'key' => 'cancelled',
            'label' => 'Cancelled',
        ],
    ];
@endphp
<div
    x-data="ordersTable({
        baseUrl: '{{ route('orders.index') }}',
        initialUrl: '{{ request()->fullUrl() }}'
    })"
    x-init="init()"
    class="space-y-8"
>
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-950">Orders</h1>
        <p class="mt-2 text-sm text-slate-500">
            View incoming orders from all channels.
        </p>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach($orderMetricCards as $card)
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($orderMetricCounts[$card['key']] ?? 0) }}</p>
            </div>
        @endforeach
    </div>

    <p class="text-sm text-slate-500">
        Metric cards show all orders. The table below follows the current filters.
    </p>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[1.4fr_.8fr_.8fr_.8fr_.8fr_auto]">
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                </svg>

                <input
                    type="search"
                    x-model="filters.search"
                    @input.debounce.450ms="goToPage(1)"
                    placeholder="Search by order or customer..."
                    class="h-12 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
            </div>

            <select x-model="filters.status" @change="goToPage(1)" class="h-12 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                <option value="">All Statuses</option>
                @foreach ($orderStatusOptions as $statusOption)
                    <option value="{{ $statusOption['value'] }}">{{ $statusOption['label'] }}</option>
                @endforeach
            </select>

            <select x-model="filters.channel" @change="goToPage(1)" class="h-12 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                <option value="">All Channels</option>
                @foreach ($orderChannelOptions as $channelOption)
                    <option value="{{ $channelOption['value'] }}">{{ $channelOption['label'] }}</option>
                @endforeach
            </select>

            <input type="date" x-model="filters.date_from" @change="goToPage(1)" class="h-12 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">

            <input type="date" x-model="filters.date_to" @change="goToPage(1)" class="h-12 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">

            <button type="button" @click="resetFilters()" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 px-4 text-sm font-black text-slate-600 transition hover:bg-slate-50">
                Reset
            </button>
        </div>
    </div>

    <div class="relative">
        <div
            x-show="loading"
            x-transition.opacity
            class="absolute inset-0 z-20 flex items-center justify-center rounded-3xl bg-white/70 backdrop-blur-sm"
        >
            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-3 shadow-lg">
                <svg class="h-5 w-5 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                </svg>
                <span class="text-sm font-bold text-slate-700">Updating results...</span>
            </div>
        </div>

        <div x-ref="table">
            @include('orders.partials.table', ['orders' => $orders])
        </div>
    </div>
</div>

@push('scripts')
<script>
    function ordersTable({ baseUrl, initialUrl }) {
        return {
            baseUrl,
            loading: false,

            filters: {
                search: '',
                status: '',
                channel: '',
                date_from: '',
                date_to: '',
                per_page: '8',
                page: '1',
            },

            init() {
                const url = new URL(initialUrl);

                this.filters.search = url.searchParams.get('search') || '';
                this.filters.status = url.searchParams.get('status') || '';
                this.filters.channel = url.searchParams.get('channel') || '';
                this.filters.date_from = url.searchParams.get('date_from') || '';
                this.filters.date_to = url.searchParams.get('date_to') || '';
                this.filters.per_page = url.searchParams.get('per_page') || '8';
                this.filters.page = url.searchParams.get('page') || '1';

                this.bindPaginationClicks();
            },

            buildUrl(page = null) {
                const url = new URL(this.baseUrl, window.location.origin);

                const params = {
                    search: this.filters.search,
                    status: this.filters.status,
                    channel: this.filters.channel,
                    date_from: this.filters.date_from,
                    date_to: this.filters.date_to,
                    per_page: this.filters.per_page,
                    page: page || this.filters.page || 1,
                };

                Object.entries(params).forEach(([key, value]) => {
                    if (value !== null && value !== undefined && value !== '') {
                        url.searchParams.set(key, value);
                    }
                });

                return url;
            },

            async fetchTable(url) {
                this.loading = true;

                try {
                    const response = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load orders.');
                    }

                    const html = await response.text();

                    this.$refs.table.innerHTML = html;

                    window.history.pushState({}, '', url.toString());

                    this.filters.page = url.searchParams.get('page') || '1';

                    this.$nextTick(() => {
                        this.bindPaginationClicks();
                    });
                } catch (error) {
                    console.error(error);
                } finally {
                    this.loading = false;
                }
            },

            goToPage(page) {
                this.filters.page = page.toString();
                const url = this.buildUrl(page);
                this.fetchTable(url);
            },

            changePerPage(value) {
                this.filters.per_page = value;
                this.goToPage(1);
            },

            resetFilters() {
                this.filters.search = '';
                this.filters.status = '';
                this.filters.channel = '';
                this.filters.date_from = '';
                this.filters.date_to = '';
                this.filters.per_page = '8';

                this.goToPage(1);
            },

            bindPaginationClicks() {
                this.$refs.table
                    .querySelectorAll('[data-pagination-link]')
                    .forEach((link) => {
                        link.addEventListener('click', (event) => {
                            event.preventDefault();

                            const url = new URL(link.href);
                            const page = url.searchParams.get('page') || '1';

                            this.goToPage(page);
                        });
                    });
            }
        }
    }
</script>
@endpush
@endsection
