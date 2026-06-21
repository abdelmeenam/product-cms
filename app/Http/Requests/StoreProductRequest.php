<?php

namespace App\Http\Requests;

use App\enums\ProductStatus;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    private const MAX_IMAGE_SIZE = 5120; // 5MB

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],

            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Product::class, 'sku'),
            ],

            'image' => ['nullable', 'image', 'max:'.self::MAX_IMAGE_SIZE],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::enum(ProductStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sku' => Str::upper($this->string('sku')->trim()->value()),
        ]);
    }
}
