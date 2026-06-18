<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->count(10)->active()->create();
        Product::factory()->count(3)->active()->lowStock()->create();
        Product::factory()->count(2)->active()->outOfStock()->create();
        Product::factory()->count(2)->draft()->create();
        Product::factory()->count(1)->inactive()->create();
    }
}
