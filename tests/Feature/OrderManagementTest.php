<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_orders_can_be_filtered_from_the_index_page(): void
    {
        $matchingOrder = Order::factory()->create([
            'order_number' => 'ORD-FILTER-1001',
            'customer_name' => 'Amina Saleh',
            'channel' => 'website',
            'status' => 'pending',
            'ordered_at' => now()->subDay(),
        ]);

        Order::factory()->create([
            'order_number' => 'ORD-OTHER-1002',
            'customer_name' => 'Noah Adams',
            'channel' => 'retail',
            'status' => 'completed',
            'ordered_at' => now()->subDays(5),
        ]);

        $response = $this->get(route('orders.index', [
            'search' => 'FILTER',
            'status' => 'pending',
            'channel' => 'website',
        ]));

        $response
            ->assertOk()
            ->assertSeeText($matchingOrder->order_number)
            ->assertDontSeeText('ORD-OTHER-1002');
    }

    public function test_orders_index_returns_the_table_partial_for_ajax_requests(): void
    {
        $matchingOrder = Order::factory()->create([
            'order_number' => 'ORD-AJAX-2001',
            'customer_name' => 'Leen Hassan',
            'status' => 'pending',
            'channel' => 'website',
        ]);

        $response = $this->get(route('orders.index', [
            'search' => 'AJAX',
        ]), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'text/html',
        ]);

        $response
            ->assertOk()
            ->assertSeeText($matchingOrder->order_number)
            ->assertDontSeeText('View incoming orders from all channels.');
    }

    public function test_the_order_details_page_displays_snapshot_items(): void
    {
        $product = Product::factory()->create([
            'name' => 'Wireless Keyboard',
            'sku' => 'SKU-KEY-200',
        ]);

        $order = Order::factory()->create([
            'order_number' => 'ORD-SHOW-1003',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => 75,
            'line_total' => 150,
        ]);

        $response = $this->get(route('orders.show', $order));

        $response
            ->assertOk()
            ->assertSeeText('ORD-SHOW-1003')
            ->assertSeeText('Wireless Keyboard')
            ->assertSeeText('SKU-KEY-200');
    }
}
