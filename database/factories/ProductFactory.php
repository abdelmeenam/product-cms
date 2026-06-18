<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(18),
            'sku' => fake()->unique()->bothify('SKU-#####'),
            'image' => null,
            'price' => fake()->randomFloat(2, 9, 499),
            'stock' => fake()->numberBetween(0, 150),
            'status' => fake()->randomElement(['active', 'draft', 'inactive']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => ['status' => 'active']);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['status' => 'draft']);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['status' => 'inactive']);
    }

    public function lowStock(): static
    {
        return $this->state(fn (): array => ['stock' => fake()->numberBetween(1, 10)]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (): array => ['stock' => 0]);
    }
}
