<?php

namespace App\Http\Controllers;

use App\enums\ProductStatus;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    private const ALLOWED_PER_PAGE = [7, 10, 15, 25];

    public function index(Request $request): View
    {
        $perPage = $this->getPerPage($request);

        $products = $this->buildProductQuery($request)
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return view('products.partials.table', compact('products'));
        }

        return view(
            'products.index',
            [
                'productStatusOptions' => ProductStatus::options(),
                'productStatusLabels' => $this->productStatusLabels(),
                'productStatusBadgeClasses' => $this->productStatusBadgeClasses(),
                'products' => $products,
            ]
        );
    }

    public function create(): View
    {
        return view('products.create', [
            'productStatusOptions' => ProductStatus::options(),
            'productStatusLabels' => $this->productStatusLabels(),
            'productStatusBadgeClasses' => $this->productStatusBadgeClasses(),
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
            'productStatusLabels' => $this->productStatusLabels(),
            'productStatusBadgeClasses' => $this->productStatusBadgeClasses(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $this->deleteImage($product);
            $data['image'] = $this->storeImage($request);
        }

        $product->update($data);

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

    private function buildProductQuery(Request $request): Builder
    {
        $search = $request->string('search')->trim()->value();
        $status = $request->enum('status', ProductStatus::class);
        $stock = $request->string('stock')->trim()->value();
        $sort = $request->string('sort', 'newest')->trim()->value();

        $query = Product::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, function (Builder $query) use ($status): void {
                $query->where('status', $status->value);
            });

        $this->applyStockFilter($query, $stock);
        $this->applySorting($query, $sort);

        return $query;
    }

    private function applyStockFilter(Builder $query, string $stock): void
    {
        match ($stock) {
            'in_stock' => $query->where('stock', '>', 10),
            'low_stock' => $query->whereBetween('stock', [1, 10]),
            'out_of_stock' => $query->where('stock', 0),
            default => null,
        };
    }

    private function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->oldest(),
            'price_high' => $query->orderByDesc('price'),
            'price_low' => $query->orderBy('price'),
            'stock_low' => $query->orderBy('stock'),
            default => $query->latest(),
        };
    }

    private function getPerPage(Request $request): int
    {
        $perPage = $request->integer('per_page', 7);

        return in_array($perPage, self::ALLOWED_PER_PAGE, true)
            ? $perPage
            : 7;
    }

    private function storeImage(Request $request): string
    {
        return $request->file('image')->store('products', 'public');
    }

    private function deleteImage(Product $product): void
    {
        if ($product->image !== null) {
            Storage::disk('public')->delete($product->image);
        }
    }

    /**
     * @return array<string, string>
     */
    private function productStatusLabels(): array
    {
        return collect(ProductStatus::options())
            ->pluck('label', 'value')
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function productStatusBadgeClasses(): array
    {
        return collect(ProductStatus::options())
            ->pluck('badge_classes', 'value')
            ->all();
    }
}
