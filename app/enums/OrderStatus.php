<?php

namespace App\enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return str($this->value)->headline()->value();
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::Paid, self::Completed => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::Cancelled => 'bg-rose-50 text-rose-700 ring-rose-200',
        };
    }

    /**
     * @return list<string>
     */
    public static function fulfilledValues(): array
    {
        return [
            self::Paid->value,
            self::Completed->value,
        ];
    }

    /**
     * @return list<array{value: string, label: string, badge_classes: string}>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $status): array => [
                'value' => $status->value,
                'label' => $status->label(),
                'badge_classes' => $status->badgeClasses(),
            ],
            self::cases(),
        );
    }
}
