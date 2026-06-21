<?php

namespace App\View\Components\Admin;

use App\enums\OrderStatus;
use App\enums\ProductStatus;
use BackedEnum;
use Illuminate\View\Component;
use Illuminate\View\View;

class StatusBadge extends Component
{
    private const DEFAULT_CLASSES = 'bg-slate-100 text-slate-600 ring-slate-200';

    private const DEFAULT_LABEL = 'Unavailable';

    public string $classes;

    public string $label;

    /**
     * Create a new class instance.
     */
    public function __construct(public BackedEnum|string|null $status = null)
    {
        $presentation = self::present($status);

        $this->classes = $presentation['classes'];
        $this->label = $presentation['label'];
    }

    public function render(): View
    {
        return view('components.admin.status-badge', [
            'classes' => $this->classes,
            'label' => $this->label,
        ]);
    }

    /**
     * @return array{classes: string, label: string}
     */
    public static function present(BackedEnum|string|null $status): array
    {
        $resolvedStatus = self::resolveStatusValue($status);

        return [
            'classes' => $resolvedStatus?->badgeClasses() ?? self::DEFAULT_CLASSES,
            'label' => $resolvedStatus?->label() ?? self::DEFAULT_LABEL,
        ];
    }

    private static function resolveStatusValue(BackedEnum|string|null $status): ProductStatus|OrderStatus|null
    {
        if ($status instanceof ProductStatus || $status instanceof OrderStatus) {
            return $status;
        }

        if (! is_string($status) || $status === '') {
            return null;
        }

        return ProductStatus::tryFrom($status) ?? OrderStatus::tryFrom($status);
    }
}
