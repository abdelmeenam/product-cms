<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory()->count(8)->pending()->create();
        Order::factory()->count(6)->paid()->create();
        Order::factory()->count(6)->completed()->create();
        Order::factory()->count(2)->cancelled()->create();
    }
}
