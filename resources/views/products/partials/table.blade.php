<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-white text-xs font-black text-slate-500">
                <tr>
                    <th class="px-6 py-5">Product</th>
                    <th class="px-6 py-5">SKU</th>
                    <th class="px-6 py-5">Price</th>
                    <th class="px-6 py-5">Stock</th>
                    <th class="px-6 py-5">Status</th>
                    <th class="px-6 py-5">Updated</th>
                    <th class="px-6 py-5 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($products as $product)
                    <tr class="transition hover:bg-slate-50/80">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 text-slate-300">
                                    @if($product->image)
                                        <img
                                            src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}"
                                            class="h-full w-full object-cover"
                                        >
                                    @else
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-width="2" d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14m18 0H3m18 0l-6-8-4 5-3-4-5 7"/>
                                        </svg>
                                    @endif
                                </div>

                                <div>
                                    <p class="font-black text-slate-950">{{ $product->name }}</p>
                                    <p class="mt-1 line-clamp-1 max-w-xs text-sm text-slate-500">
                                        {{ $product->description }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-5 font-semibold text-slate-500">
                            {{ $product->sku }}
                        </td>

                        <td class="px-6 py-5 font-black text-slate-700">
                            ${{ number_format($product->price, 2) }}
                        </td>

                        <td class="px-6 py-5">
                            <span class="font-black
                                {{ $product->stock === 0 ? 'text-rose-600' : '' }}
                                {{ $product->stock > 0 && $product->stock <= 10 ? 'text-amber-600' : '' }}
                                {{ $product->stock > 10 ? 'text-emerald-600' : '' }}
                            ">
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
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl text-xl font-black text-slate-500 hover:bg-slate-100"
                                >
                                    ⋮
                                </button>

                                <div
                                    x-show="open"
                                    @click.outside="open = false"
                                    x-transition
                                    class="absolute right-0 z-30 mt-2 w-40 rounded-2xl border border-slate-200 bg-white p-2 text-left shadow-xl"
                                >
                                    <a
                                        href="{{ route('products.edit', $product) }}"
                                        class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('products.destroy', $product) }}"
                                        onsubmit="return confirm('Delete this product?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="block w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50"
                                        >
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
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl font-black text-blue-600">
                                    +
                                </div>

                                <h3 class="mt-4 text-lg font-black text-slate-950">
                                    No products found
                                </h3>

                                <p class="mt-2 text-sm text-slate-500">
                                    Create your first product or adjust the filters.
                                </p>

                                <a
                                    href="{{ route('products.create') }}"
                                    class="mt-5 inline-flex rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white"
                                >
                                    Add Product
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Custom Pagination --}}
    <div class="flex flex-col gap-4 border-t border-slate-100 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
        <div class="text-sm font-medium text-slate-500">
            Showing
            <span class="font-black text-slate-950">{{ $products->firstItem() ?? 0 }}</span>
            to
            <span class="font-black text-slate-950">{{ $products->lastItem() ?? 0 }}</span>
            of
            <span class="font-black text-slate-950">{{ $products->total() }}</span>
            products
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-slate-500">Rows per page</span>

                <select
                    x-model="filters.per_page"
                    @change="changePerPage($event.target.value)"
                    class="h-11 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-black text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
                    <option value="7">7</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                {{-- Previous --}}
                @if($products->onFirstPage())
                    <span class="inline-flex h-11 w-11 cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-300">
                        ‹
                    </span>
                @else
                    <a
                        href="{{ $products->previousPageUrl() }}"
                        data-pagination-link
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                    >
                        ‹
                    </a>
                @endif

                @php
                    $currentPage = $products->currentPage();
                    $lastPage = $products->lastPage();

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

                {{-- First page --}}
                @if($start > 1)
                    <a
                        href="{{ $products->url(1) }}"
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

                {{-- Middle pages --}}
                @for($page = $start; $page <= $end; $page++)
                    @if($page === $currentPage)
                        <span class="inline-flex h-11 min-w-11 items-center justify-center rounded-2xl bg-blue-600 px-4 text-sm font-black text-white shadow-lg shadow-blue-600/20">
                            {{ $page }}
                        </span>
                    @else
                        <a
                            href="{{ $products->url($page) }}"
                            data-pagination-link
                            class="inline-flex h-11 min-w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-black text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                {{-- Last page --}}
                @if($end < $lastPage)
                    @if($end < $lastPage - 1)
                        <span class="inline-flex h-11 min-w-11 items-center justify-center px-2 text-sm font-black text-slate-400">
                            ...
                        </span>
                    @endif

                    <a
                        href="{{ $products->url($lastPage) }}"
                        data-pagination-link
                        class="inline-flex h-11 min-w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-black text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                    >
                        {{ $lastPage }}
                    </a>
                @endif

                {{-- Next --}}
                @if($products->hasMorePages())
                    <a
                        href="{{ $products->nextPageUrl() }}"
                        data-pagination-link
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                    >
                        ›
                    </a>
                @else
                    <span class="inline-flex h-11 w-11 cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-300">
                        ›
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
