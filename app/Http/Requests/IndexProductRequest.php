<?php

namespace App\Http\Requests;

use App\enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexProductRequest extends FormRequest
{
    private const DEFAULT_PER_PAGE = 7;

    private const ALLOWED_PER_PAGE = [7, 10, 15, 25];

    private const STOCK_FILTERS = [
        'in_stock',
        'low_stock',
        'out_of_stock',
    ];

    private const SORT_OPTIONS = [
        'newest',
        'oldest',
        'price_high',
        'price_low',
        'stock_low',
    ];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(ProductStatus::class)],
            'stock' => ['nullable', 'string', Rule::in(self::STOCK_FILTERS)],
            'sort' => ['nullable', 'string', Rule::in(self::SORT_OPTIONS)],
            'per_page' => ['nullable', 'integer', Rule::in(self::ALLOWED_PER_PAGE)],
        ];
    }

    /**
     * @return array{
     *     search: string,
     *     status: ProductStatus|null,
     *     stock: string,
     *     sort: string
     * }
     */
    public function filters(): array
    {
        return [
            'search' => $this->string('search')->trim()->value(),
            'status' => $this->enum('status', ProductStatus::class),
            'stock' => $this->string('stock')->trim()->value(),
            'sort' => $this->string('sort', 'newest')->trim()->value(),
        ];
    }

    public function perPage(): int
    {
        $perPage = $this->integer('per_page', self::DEFAULT_PER_PAGE);

        return in_array($perPage, self::ALLOWED_PER_PAGE, true)
            ? $perPage
            : self::DEFAULT_PER_PAGE;
    }
}
