<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $status = $request->string('status')->trim()->value();
        $stock = $request->string('stock')->trim()->value();

        $products = Product::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $nestedQuery) use ($search): void {
                    $nestedQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn (Builder $query): Builder => $query->where('status', $status))
            ->when($stock === 'in_stock', fn (Builder $query): Builder => $query->where('stock', '>', 10))
            ->when($stock === 'low_stock', fn (Builder $query): Builder => $query->whereBetween('stock', [1, 10]))
            ->when($stock === 'out_of_stock', fn (Builder $query): Builder => $query->where('stock', 0));

        match ((string) $request->get('sort', 'newest')) {
            'oldest' => $products->oldest(),
            'price_high' => $products->orderByDesc('price'),
            'price_low' => $products->orderBy('price'),
            'stock_low' => $products->orderBy('stock'),
            default => $products->latest(),
        };

        return view('products.index', [
            'products' => $products->paginate(7)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::query()->create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load([
            'orderItems' => static fn (HasMany $query) => $query
                ->with('order:id,order_number,status,ordered_at')
                ->latest()
                ->limit(5),
        ]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image !== null) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image !== null) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
