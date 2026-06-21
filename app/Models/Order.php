<?php

namespace App\Models;

use App\enums\OrderChannel;
use App\enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'channel',
        'status',
        'total',
        'ordered_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $attributes = [
        'channel' => OrderChannel::Website->value,
        'status' => OrderStatus::Pending->value,
        'total' => '0.00',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'channel' => OrderChannel::class,
            'total' => 'decimal:2',
            'ordered_at' => 'datetime',
            'status' => OrderStatus::class,
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeFilterIndex(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $query->where(function (Builder $nestedQuery) use ($filters): void {
                    $nestedQuery
                        ->where('order_number', 'like', "%{$filters['search']}%")
                        ->orWhere('customer_name', 'like', "%{$filters['search']}%")
                        ->orWhere('customer_email', 'like', "%{$filters['search']}%");
                });
            })
            ->when($filters['status'] !== null, function (Builder $query) use ($filters): void {
                $query->where('status', $filters['status']->value);
            })
            ->when($filters['channel'] !== null, function (Builder $query) use ($filters): void {
                $query->where('channel', $filters['channel']->value);
            })
            ->when($filters['date_from'] !== '', function (Builder $query) use ($filters): void {
                $query->whereDate('ordered_at', '>=', $filters['date_from']);
            })
            ->when($filters['date_to'] !== '', function (Builder $query) use ($filters): void {
                $query->whereDate('ordered_at', '<=', $filters['date_to']);
            });
    }
}
