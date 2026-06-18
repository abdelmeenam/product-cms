@php($product = $product ?? null)

<div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
    <div class="space-y-5">
        <div>
            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $product?->name) }}" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" required>
            @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">Description</label>
            <textarea id="description" name="description" rows="7" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" required>{{ old('description', $product?->description) }}</textarea>
            @error('description') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="sku" class="mb-2 block text-sm font-semibold text-slate-700">SKU</label>
                <input id="sku" name="sku" type="text" value="{{ old('sku', $product?->sku) }}" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm uppercase outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" required>
                @error('sku') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
                <select id="status" name="status" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" required>
                    @foreach (['active', 'draft', 'inactive'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $product?->status ?? 'active') === $status)>{{ str($status)->headline() }}</option>
                    @endforeach
                </select>
                @error('status') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="space-y-5">
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-1">
            <div>
                <label for="price" class="mb-2 block text-sm font-semibold text-slate-700">Price</label>
                <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product?->price) }}" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" required>
                @error('price') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="stock" class="mb-2 block text-sm font-semibold text-slate-700">Stock</label>
                <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product?->stock ?? 0) }}" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" required>
                @error('stock') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="image" class="mb-2 block text-sm font-semibold text-slate-700">Image</label>
            <input id="image" name="image" type="file" accept="image/*" class="block w-full rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
            @error('image') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        @if ($product?->image)
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-100">
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-52 w-full object-cover">
            </div>
        @endif
    </div>
</div>

<div class="mt-8 flex flex-wrap items-center gap-3">
    <button type="submit" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
        {{ $submitLabel }}
    </button>
    <a href="{{ route('products.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
        Cancel
    </a>
</div>
