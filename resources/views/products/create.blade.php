@extends('layouts.admin', ['title' => 'Create Product'])

@section('content')
<div
    x-data="{
        name: '',
        sku: '',
        description: '',
        price: '',
        stock: '',
        statusOptions: @js($productStatusOptions),
        status: @js(old('status', $productStatusOptions[0]['value'])),
        imagePreview: null,
        previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.imagePreview = URL.createObjectURL(file);
        },
        currentStatusOption() {
            return this.statusOptions.find((statusOption) => statusOption.value === this.status) || null;
        },
        currentStatusLabel() {
            const statusOption = this.currentStatusOption();

            return statusOption ? statusOption.label : this.status;
        },
        currentStatusClasses() {
            const statusOption = this.currentStatusOption();

            return statusOption ? statusOption.badge_classes : 'bg-slate-100 text-slate-600 ring-slate-200';
        }
    }"
    class="space-y-8"
>
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-950">Create Product</h1>
        <div class="mt-2 flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('products.index') }}" class="font-bold text-blue-700">Products</a>
            <span>/</span>
            <span>Create Product</span>
        </div>
        <p class="mt-2 text-sm text-slate-500">
            Add the essential details about your product. Only fields below are required.
        </p>
    </div>

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid gap-6 xl:grid-cols-[1.4fr_.8fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Product Information</h2>

                <div class="mt-6 space-y-6">
                    <div>
                        <label class="text-sm font-black text-slate-700">Product Name <span class="text-rose-500">*</span></label>
                        <p class="mt-1 text-xs text-slate-500">Enter a clear, descriptive name for your product.</p>
                        <input
                            x-model="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="e.g. Wireless Bluetooth Headphones"
                            class="mt-3 h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                        >
                        @error('name') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-black text-slate-700">SKU <span class="text-rose-500">*</span></label>
                            <p class="mt-1 text-xs text-slate-500">Unique identifier for your product.</p>
                            <input
                                x-model="sku"
                                type="text"
                                name="sku"
                                value="{{ old('sku') }}"
                                placeholder="e.g. WHP-ANC-BLK"
                                class="mt-3 h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >
                            @error('sku') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-black text-slate-700">Price <span class="text-rose-500">*</span></label>
                            <p class="mt-1 text-xs text-slate-500">Set the selling price for your product.</p>
                            <div class="mt-3 flex h-12 overflow-hidden rounded-2xl border border-slate-200 focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10">
                                <span class="flex w-12 items-center justify-center border-r border-slate-200 bg-slate-50 text-sm font-bold text-slate-500">$</span>
                                <input
                                    x-model="price"
                                    type="number"
                                    step="0.01"
                                    name="price"
                                    value="{{ old('price') }}"
                                    placeholder="e.g. 89.99"
                                    class="w-full border-0 px-4 text-sm outline-none"
                                >
                            </div>
                            @error('price') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-black text-slate-700">Description <span class="text-rose-500">*</span></label>
                        <p class="mt-1 text-xs text-slate-500">Provide a short description to help customers understand your product.</p>
                        <textarea
                            x-model="description"
                            name="description"
                            rows="5"
                            maxlength="1000"
                            placeholder="e.g. High-quality noise cancelling headphones with premium sound and long battery life."
                            class="mt-3 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                        >{{ old('description') }}</textarea>
                        @error('description') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-black text-slate-700">Product Image</label>
                            <p class="mt-1 text-xs text-slate-500">Upload one clear product image.</p>

                            <label class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center transition hover:border-blue-300 hover:bg-blue-50/40">
                                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-width="2" d="M12 16V4m0 0l-4 4m4-4l4 4M20 16.5A4.5 4.5 0 0 1 15.5 21h-7A4.5 4.5 0 0 1 4 16.5"/>
                                </svg>
                                <span class="mt-3 text-sm font-black text-slate-700">Drag and drop an image here</span>
                                <span class="mt-1 text-sm text-slate-500">or <span class="font-bold text-blue-700">click to browse</span></span>
                                <span class="mt-3 text-xs text-slate-400">PNG, JPG, WEBP up to 5MB</span>
                                <input type="file" name="image" accept="image/*" class="hidden" @change="previewImage">
                            </label>

                            @error('image') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="text-sm font-black text-slate-700">Stock <span class="text-rose-500">*</span></label>
                                <p class="mt-1 text-xs text-slate-500">Enter the available stock quantity.</p>
                                <input
                                    x-model="stock"
                                    type="number"
                                    name="stock"
                                    value="{{ old('stock') }}"
                                    placeholder="e.g. 100"
                                    class="mt-3 h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                >
                                @error('stock') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-black text-slate-700">Status <span class="text-rose-500">*</span></label>
                                <p class="mt-1 text-xs text-slate-500">Choose the publishing state for this product.</p>
                                <select
                                    x-model="status"
                                    name="status"
                                    class="mt-3 h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm font-bold text-slate-700 outline-none focus:border-blue-500"
                                >
                                    @foreach ($productStatusOptions as $statusOption)
                                        <option value="{{ $statusOption['value'] }}">{{ $statusOption['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('status') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live Preview --}}
            <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Live Preview</h2>
                <p class="mt-1 text-sm text-slate-500">This is how your product will appear in the store.</p>

                <div class="mt-6 rounded-3xl border border-slate-200 p-4">
                    <div class="relative overflow-hidden rounded-3xl bg-slate-50">
                        <span
                            class="absolute left-4 top-4 rounded-xl px-3 py-1 text-xs font-black ring-1"
                            :class="currentStatusClasses()"
                            x-text="currentStatusLabel()"
                        ></span>

                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="h-72 w-full object-cover">
                        </template>

                        <template x-if="!imagePreview">
                            <div class="flex h-72 items-center justify-center text-slate-300">
                                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-width="2" d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14m18 0H3m18 0l-6-8-4 5-3-4-5 7"/>
                                </svg>
                            </div>
                        </template>
                    </div>

                    <div class="mt-5">
                        <h3 class="text-xl font-black text-slate-950" x-text="name || 'Product Name'"></h3>
                        <p class="mt-2 text-2xl font-black text-blue-700">
                            $<span x-text="price || '0.00'"></span>
                        </p>
                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500" x-text="description || 'Product description will appear here.'"></p>

                        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-xs font-bold text-slate-500">
                            <span>SKU: <span x-text="sku || 'SKU-CODE'"></span></span>
                            <span>Stock: <span x-text="stock || '0'"></span></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm font-semibold leading-6 text-blue-700">
                    Preview updates automatically as you fill in the details.
                </div>
            </aside>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('products.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 hover:bg-slate-50">
                Cancel
            </a>

            <button class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                Save Product
            </button>
        </div>
    </form>
</div>
@endsection
