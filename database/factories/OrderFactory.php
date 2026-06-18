<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => fake()->unique()->bothify('ORD-########'),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->boolean(85) ? fake()->safeEmail() : null,
            'channel' => fake()->randomElement(['website', 'instagram', 'retail', 'whatsapp']),
            'status' => fake()->randomElement(['pending', 'paid', 'completed', 'cancelled']),
            'total' => 0,
            'ordered_at' => fake()->dateTimeBetween('-45 days', 'now'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (): array => ['status' => 'pending']);
    }

    public function paid(): static
    {
        return $this->state(fn (): array => ['status' => 'paid']);
    }

    public function completed(): static
    {
        return $this->state(fn (): array => ['status' => 'completed']);
    }

    public function cancelled(): static
    {
        return $this->state(fn (): array => ['status' => 'cancelled']);
    }
}
