@extends('layouts.admin', ['title' => 'Products'])

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950">Products</h1>
            <p class="mt-2 text-sm text-slate-500">
                Manage your catalogue with simple product information.
            </p>
        </div>

        <a href="{{ route('products.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
            <span class="text-lg">+</span>
            Add Product
        </a>
    </div>

    {{-- Filters --}}
    <form
        method="GET"
        action="{{ route('products.index') }}"
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
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[1.5fr_1fr_1fr_1fr]">
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                </svg>
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    @input="submitForm"
                    placeholder="Search by name or SKU..."
                    class="h-13 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
            </div>

            <select name="status" @change="$refs.form.submit()" class="h-13 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 outline-none focus:border-blue-500">
                <option value="">All Status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>

            <select name="stock" @change="$refs.form.submit()" class="h-13 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 outline-none focus:border-blue-500">
                <option value="">All Stock</option>
                <option value="in_stock" @selected(request('stock') === 'in_stock')>In Stock</option>
                <option value="low_stock" @selected(request('stock') === 'low_stock')>Low Stock</option>
                <option value="out_of_stock" @selected(request('stock') === 'out_of_stock')>Out of Stock</option>
            </select>

            <select name="sort" @change="$refs.form.submit()" class="h-13 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 outline-none focus:border-blue-500">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest First</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest First</option>
                <option value="price_high" @selected(request('sort') === 'price_high')>Price High</option>
                <option value="price_low" @selected(request('sort') === 'price_low')>Price Low</option>
                <option value="stock_low" @selected(request('sort') === 'stock_low')>Stock Low</option>
            </select>
        </div>
    </form>

    {{-- Table --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">SKU</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Stock</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Updated</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="h-14 w-14 overflow-hidden rounded-2xl border border-slate-100 bg-slate-100">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-slate-300">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-width="2" d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14m18 0H3m18 0l-6-8-4 5-3-4-5 7"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="font-black text-slate-950">{{ $product->name }}</p>
                                        <p class="mt-1 line-clamp-1 max-w-xs text-xs text-slate-500">
                                            {{ $product->description }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5 font-semibold text-slate-500">{{ $product->sku }}</td>
                            <td class="px-6 py-5 font-black text-slate-950">${{ number_format($product->price, 2) }}</td>

                            <td class="px-6 py-5">
                                <span class="font-black {{ $product->stock === 0 ? 'text-rose-600' : ($product->stock <= 10 ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ $product->stock }}
                                </span>
                            </td>

                            <td class="px-6 py-5">
                                <x-admin.status-badge :status="$product->status" />
                            </td>

                            <td class="px-6 py-5 text-slate-500">
                                <div>{{ $product->updated_at->format('M d, Y') }}</div>
                                <div class="text-xs">{{ $product->updated_at->format('h:i A') }}</div>
                            </td>

                            <td class="px-6 py-5 text-right">
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" class="rounded-xl border border-slate-200 p-2 text-slate-500 hover:bg-slate-50">
                                        ⋮
                                    </button>

                                    <div
                                        x-show="open"
                                        @click.outside="open = false"
                                        x-transition
                                        class="absolute right-0 z-20 mt-2 w-40 rounded-2xl border border-slate-200 bg-white p-2 text-left shadow-xl"
                                    >
                                        <a href="{{ route('products.edit', $product) }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="block w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="mx-auto max-w-sm">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                        +
                                    </div>
                                    <h3 class="mt-4 text-lg font-black text-slate-950">No products found</h3>
                                    <p class="mt-2 text-sm text-slate-500">
                                        Create your first product or adjust the filters.
                                    </p>
                                    <a href="{{ route('products.create') }}" class="mt-5 inline-flex rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white">
                                        Add Product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-6 py-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
