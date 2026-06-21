<?php

namespace App\Models;

use App\enums\OrderChannel;
use App\enums\OrderStatus;
use Database\Factories\OrderFactory;
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
}
