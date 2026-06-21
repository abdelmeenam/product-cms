<?php

namespace App\Http\Requests;

use App\enums\OrderChannel;
use App\enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IndexOrderRequest extends FormRequest
{
    private const DEFAULT_PER_PAGE = 8;

    private const PER_PAGE_OPTIONS = [8, 10, 15, 25];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(OrderStatus::class)],
            'channel' => ['nullable', Rule::enum(OrderChannel::class)],
            'date_from' => ['nullable', Rule::date()->format('Y-m-d')],
            'date_to' => ['nullable', Rule::date()->format('Y-m-d'), 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', Rule::in(self::PER_PAGE_OPTIONS)],
        ];
    }

    /**
     * @return array{
     *     search: string,
     *     status: OrderStatus|null,
     *     channel: OrderChannel|null,
     *     date_from: string,
     *     date_to: string
     * }
     */
    public function filters(): array
    {
        return [
            'search' => $this->string('search')->trim()->value(),
            'status' => $this->enum('status', OrderStatus::class),
            'channel' => $this->enum('channel', OrderChannel::class),
            'date_from' => $this->string('date_from')->trim()->value(),
            'date_to' => $this->string('date_to')->trim()->value(),
        ];
    }

    public function perPage(): int
    {
        return $this->integer('per_page', self::DEFAULT_PER_PAGE);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'search' => Str::of($this->input('search', ''))->trim()->value() ?: null,
            'status' => $this->filled('status') ? $this->input('status') : null,
            'channel' => $this->filled('channel') ? $this->input('channel') : null,
            'date_from' => $this->filled('date_from') ? $this->input('date_from') : null,
            'date_to' => $this->filled('date_to') ? $this->input('date_to') : null,
        ]);
    }
}
