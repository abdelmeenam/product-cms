<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::query()->get();

        if ($products->isEmpty()) {
            return;
        }

        Order::query()->get()->each(function (Order $order) use ($products): void {
            $total = 0.0;
            $itemCount = fake()->numberBetween(1, 4);

            foreach ($products->random($itemCount) as $product) {
                $quantity = fake()->numberBetween(1, 3);
                $unitPrice = (float) $product->price;
                $lineTotal = round($quantity * $unitPrice, 2);

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);

                $total += $lineTotal;
            }

            $order->update(['total' => round($total, 2)]);
        });
    }
}
