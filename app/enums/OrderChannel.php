<?php

namespace App\enums;

enum OrderChannel: string
{
    case Website = 'website';
    case Instagram = 'instagram';
    case Retail = 'retail';
    case Whatsapp = 'whatsapp';

    public function label(): string
    {
        return str($this->value)->headline()->value();
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $channel): string => $channel->value,
            self::cases(),
        );
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $channel): array => [
                'value' => $channel->value,
                'label' => $channel->label(),
            ],
            self::cases(),
        );
    }
}
