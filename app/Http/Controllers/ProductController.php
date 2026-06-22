<?php

namespace App\Http\Controllers;

use App\enums\ProductStatus;
use App\Http\Requests\IndexProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(IndexProductRequest $request): View|Response
    {
        $products = Product::query()
            ->filterIndex($request->filters())
            ->paginate($request->perPage())
            ->withQueryString();

        if ($request->ajax()) {
            return response()->view('products.partials.table', [
                'products' => $products,
            ]);
        }

        return view('products.index', [
            'productStatusOptions' => ProductStatus::options(),
            'products' => $products,
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'productStatusOptions' => ProductStatus::options(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request);
        }

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'productStatusOptions' => ProductStatus::options(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $oldImage = null;

        if ($request->hasFile('image')) {
            $oldImage = $product->image;
            $data['image'] = $this->storeImage($request);
        }

        $product->update($data);

        if ($oldImage !== null) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteImage($product);

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    private function storeImage(StoreProductRequest|UpdateProductRequest $request): string
    {
        return $request->file('image')->store('products', 'public');
    }

    private function deleteImage(Product $product): void
    {
        if ($product->image !== null) {
            Storage::disk('public')->delete($product->image);
        }
    }
}
