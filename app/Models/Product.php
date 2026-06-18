<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
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
}
