@extends('layouts.admin')

@section('title', $product->name)

@section('content')
    <div class="space-y-8">
        <section class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-blue-600">Product Details</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">{{ $product->name }}</h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">Review pricing, status, inventory, and recent order activity for this catalogue item.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('products.edit', $product) }}" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">Edit Product</a>
                <a href="{{ route('products.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Back to Products</a>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                @if ($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-80 w-full object-cover">
                @else
                    <div class="flex h-80 items-center justify-center bg-linear-to-br from-blue-50 via-slate-100 to-violet-50 text-7xl font-bold text-slate-400">
                        {{ strtoupper(substr($product->name, 0, 1)) }}
                    </div>
                @endif

                <div class="space-y-5 p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $product->sku }}</p>
                            <h2 class="mt-2 text-3xl font-bold text-slate-950">{{ $product->name }}</h2>
                        </div>

                        <x-admin.status-badge :status="$product->status" />
                    </div>

                    <p class="leading-7 text-slate-600">{{ $product->description }}</p>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm text-slate-500">Price</p>
                            <p class="mt-2 text-2xl font-bold text-slate-950">{{ \Illuminate\Support\Number::currency((float) $product->price) }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm text-slate-500">Stock</p>
                            <p class="mt-2 text-2xl font-bold {{ $product->is_out_of_stock ? 'text-rose-600' : ($product->is_low_stock ? 'text-amber-600' : 'text-slate-950') }}">{{ $product->stock }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-950">Recent Order Activity</h2>
                        <p class="mt-1 text-sm text-slate-500">Latest order items linked to this product.</p>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse ($product->orderItems as $item)
                        <div class="rounded-2xl border border-slate-200 px-4 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-950">{{ $item->order?->order_number ?? 'Archived order' }}</p>
                                    <p class="text-sm text-slate-500">{{ optional($item->order?->ordered_at)->format('M d, Y') ?? 'No order date' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-slate-950">{{ $item->quantity }} units</p>
                                    <p class="text-sm text-slate-500">{{ \Illuminate\Support\Number::currency((float) $item->line_total) }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-3xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                            This product has not been ordered yet.
                        </p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
