<?php

namespace App\enums;

enum ProductStatus: string
{
    case Active = 'active';
    case Draft = 'draft';
    case Inactive = 'inactive';

    public function label(): string
    {
        return str($this->value)->headline()->value();
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Active => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::Draft => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::Inactive => 'bg-rose-50 text-rose-700 ring-rose-200',
        };
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
