<?php

namespace App\Models;

use App\enums\ProductStatus;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'sku',
        'image',
        'price',
        'stock',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'status' => ProductStatus::class,
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected function isLowStock(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->stock > 0 && $this->stock <= 10,
        );
    }

    protected function isOutOfStock(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->stock === 0,
        );
    }

    public function scopeFilterIndex(Builder $query, array $filters): Builder
    {
        $search = $filters['search'];
        $status = $filters['status'];
        $stock = $filters['stock'];
        $sort = $filters['sort'];

        $query
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, function (Builder $query) use ($status): void {
                $query->where('status', $status->value);
            });

        match ($stock) {
            'in_stock' => $query->where('stock', '>', 10),
            'low_stock' => $query->whereBetween('stock', [1, 10]),
            'out_of_stock' => $query->where('stock', 0),
            default => null,
        };

        match ($sort) {
            'oldest' => $query->oldest(),
            'price_high' => $query->orderByDesc('price'),
            'price_low' => $query->orderBy('price'),
            'stock_low' => $query->orderBy('stock'),
            default => $query->latest(),
        };

        return $query;
    }
}
