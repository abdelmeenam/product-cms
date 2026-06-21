@extends('layouts.admin', ['title' => 'Products'])

@section('content')
<div
    x-data="productsTable({
        baseUrl: '{{ route('products.index') }}',
        initialUrl: '{{ request()->fullUrl() }}'
    })"
    x-init="init()"
    class="space-y-8"
>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950">Products</h1>
            <p class="mt-2 text-sm text-slate-500">
                Manage your catalogue with simple product information.
            </p>
        </div>

        <a
            href="{{ route('products.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
        >
            <span class="text-lg">+</span>
            Add Product
        </a>
    </div>

    {{-- Filters --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[1.4fr_1fr_1fr_1fr_.8fr]">
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                </svg>

                <input
                    type="search"
                    x-model="filters.search"
                    @input.debounce.450ms="goToPage(1)"
                    placeholder="Search by name or SKU..."
                    class="h-13 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
            </div>

            <select
                x-model="filters.status"
                @change="goToPage(1)"
                class="h-13 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
            >
                <option value="">All Status</option>
                @foreach ($productStatusOptions as $statusOption)
                    <option value="{{ $statusOption['value'] }}">{{ $statusOption['label'] }}</option>
                @endforeach
            </select>

            <select
                x-model="filters.stock"
                @change="goToPage(1)"
                class="h-13 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
            >
                <option value="">All Stock</option>
                <option value="in_stock">In Stock</option>
                <option value="low_stock">Low Stock</option>
                <option value="out_of_stock">Out of Stock</option>
            </select>

            <select
                x-model="filters.sort"
                @change="goToPage(1)"
                class="h-13 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
            >
                <option value="newest">Sort: Newest First</option>
                <option value="oldest">Sort: Oldest First</option>
                <option value="price_high">Price High</option>
                <option value="price_low">Price Low</option>
                <option value="stock_low">Stock Low</option>
            </select>

        </div>
    </div>

    {{-- Table Area --}}
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
            @include('products.partials.table', ['products' => $products])
        </div>
    </div>
</div>

@push('scripts')
<script>
    function productsTable({ baseUrl, initialUrl }) {
        return {
            baseUrl,
            loading: false,

            filters: {
                search: '',
                status: '',
                stock: '',
                sort: 'newest',
                per_page: '7',
                page: '1',
            },

            init() {
                const url = new URL(initialUrl);

                this.filters.search = url.searchParams.get('search') || '';
                this.filters.status = url.searchParams.get('status') || '';
                this.filters.stock = url.searchParams.get('stock') || '';
                this.filters.sort = url.searchParams.get('sort') || 'newest';
                this.filters.per_page = url.searchParams.get('per_page') || '7';
                this.filters.page = url.searchParams.get('page') || '1';

                this.bindPaginationClicks();
            },

            buildUrl(page = null) {
                const url = new URL(this.baseUrl, window.location.origin);

                const params = {
                    search: this.filters.search,
                    status: this.filters.status,
                    stock: this.filters.stock,
                    sort: this.filters.sort,
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
                        throw new Error('Failed to load products.');
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
