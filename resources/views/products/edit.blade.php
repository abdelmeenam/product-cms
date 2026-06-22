@extends('layouts.admin', ['title' => 'Edit Product'])

@section('content')
<div
    x-data="{
        name: @js(old('name', $product->name)),
        sku: @js(old('sku', $product->sku)),
        description: @js(old('description', $product->description)),
        price: @js(old('price', $product->price)),
        stock: @js(old('stock', $product->stock)),
        statusOptions: @js($productStatusOptions),
        status: @js(old('status', $product->status->value)),
        imagePreview: @js($product->image ? asset('storage/' . $product->image) : null),
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
        <h1 class="text-3xl font-bold tracking-tight text-slate-950">Edit Product</h1>
        <div class="mt-2 flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('products.index') }}" class="font-bold text-blue-700">Products</a>
            <span>/</span>
            <span>Edit Product</span>
        </div>
        <p class="mt-2 text-sm text-slate-500">
            Update product catalogue information.
        </p>
    </div>

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid gap-6 xl:grid-cols-[1.4fr_.8fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Product Information</h2>

                <div class="mt-6 space-y-6">
                    <div>
                        <label class="text-sm font-black text-slate-700">Product Name</label>
                        <input x-model="name" type="text" name="name" class="mt-3 h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-blue-500" />
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-black text-slate-700">SKU</label>
                            <input x-model="sku" type="text" name="sku" class="mt-3 h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-blue-500" />
                        </div>

                        <div>
                            <label class="text-sm font-black text-slate-700">Price</label>
                            <input x-model="price" type="number" step="0.01" name="price" class="mt-3 h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-blue-500" />
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-black text-slate-700">Description</label>
                        <textarea x-model="description" name="description" rows="5" class="mt-3 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500"></textarea>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-black text-slate-700">Product Image</label>
                            <label class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center hover:border-blue-300">
                                <span class="text-sm font-black text-slate-700">Upload new image</span>
                                <span class="mt-1 text-sm text-slate-500">or keep current image</span>
                                <input type="file" name="image" accept="image/*" class="hidden" @change="previewImage">
                            </label>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="text-sm font-black text-slate-700">Stock</label>
                                <input x-model="stock" type="number" name="stock" class="mt-3 h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-blue-500" />
                            </div>

                            <div>
                                <label class="text-sm font-black text-slate-700">Status</label>
                                <select x-model="status" name="status" class="mt-3 h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm font-bold text-slate-700 outline-none focus:border-blue-500">
                                    @foreach ($productStatusOptions as $statusOption)
                                        <option value="{{ $statusOption['value'] }}">{{ $statusOption['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Live Preview</h2>

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
                    </div>

                    <h3 class="mt-5 text-xl font-black text-slate-950" x-text="name"></h3>
                    <p class="mt-2 text-2xl font-black text-blue-700">$<span x-text="price"></span></p>
                    <p class="mt-3 text-sm leading-6 text-slate-500" x-text="description"></p>
                </div>
            </aside>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('products.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700">
                Cancel
            </a>

            <button class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20">
                Update Product
            </button>
        </div>
    </form>
</div>
@endsection
