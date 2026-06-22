<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_the_overview_page_displays_core_metrics(): void
    {
        $product = Product::factory()->create([
            'name' => 'Desk Lamp',
            'sku' => 'SKU-LAMP',
            'price' => 49.99,
        ]);

        $paidOrder = Order::factory()->paid()->create([
            'order_number' => 'ORD-PAID-1001',
            'total' => 149.97,
        ]);

        $pendingOrder = Order::factory()->pending()->create([
            'order_number' => 'ORD-PENDING-1002',
            'total' => 25.00,
        ]);

        OrderItem::factory()->create([
            'order_id' => $paidOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 3,
            'unit_price' => 49.99,
            'line_total' => 149.97,
        ]);

        OrderItem::factory()->create([
            'order_id' => $pendingOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 1,
            'unit_price' => 25.00,
            'line_total' => 25.00,
        ]);

        $response = $this->get(route('overview'));

        $response
            ->assertOk()
            ->assertSeeText('Overview')
            ->assertSeeText('ORD-PAID-1001')
            ->assertSeeText('Desk Lamp');
    }
}
