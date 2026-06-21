<?php

namespace Tests\Feature;

use App\enums\ProductStatus;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_products_can_be_filtered_from_the_listing(): void
    {
        Product::factory()->create([
            'name' => 'Active Chair',
            'sku' => 'SKU-ACTIVE-1',
            'status' => 'active',
            'stock' => 18,
        ]);

        Product::factory()->create([
            'name' => 'Draft Chair',
            'sku' => 'SKU-DRAFT-1',
            'status' => 'draft',
            'stock' => 4,
        ]);

        $response = $this->get(route('products.index', [
            'search' => 'Active',
            'status' => 'active',
            'stock' => 'in_stock',
        ]));

        $response
            ->assertOk()
            ->assertSeeText('Active Chair')
            ->assertDontSeeText('Draft Chair');
    }

    public function test_a_product_can_be_created_with_an_image(): void
    {
        $diskRoot = $this->useTemporaryPublicDisk();

        $response = $this->post(route('products.store'), [
            'name' => 'Studio Speaker',
            'description' => 'Premium speaker for office and home desks.',
            'sku' => 'sku-speaker-100',
            'image' => UploadedFile::fake()->image('speaker.png'),
            'price' => 189.90,
            'stock' => 12,
            'status' => 'active',
        ]);

        $product = Product::query()->firstOrFail();

        $response->assertRedirect(route('products.index'));
        $this->assertModelExists($product);
        $this->assertSame('SKU-SPEAKER-100', $product->sku);
        $this->assertNotNull($product->image);

        $this->assertFileExists($diskRoot.DIRECTORY_SEPARATOR.$product->image);
    }

    public function test_updating_a_product_replaces_its_existing_image(): void
    {
        $diskRoot = $this->useTemporaryPublicDisk();

        Storage::disk('public')->put('products/old-image.jpg', 'old-image');

        $product = Product::factory()->create([
            'image' => 'products/old-image.jpg',
        ]);

        $response = $this->put(route('products.update', $product), [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'sku' => $product->sku,
            'image' => UploadedFile::fake()->image('new-image.jpg'),
            'price' => 99.50,
            'stock' => 9,
            'status' => 'inactive',
        ]);

        $product->refresh();

        $response->assertRedirect(route('products.index'));
        $this->assertFileDoesNotExist($diskRoot.DIRECTORY_SEPARATOR.'products/old-image.jpg');
        $this->assertFileExists($diskRoot.DIRECTORY_SEPARATOR.$product->image);
        $this->assertSame('Updated Product', $product->name);
        $this->assertSame(ProductStatus::Inactive, $product->status);
    }

    private function useTemporaryPublicDisk(): string
    {
        $diskRoot = base_path('tests/.runtime/disks/'.(string) str()->uuid());

        File::ensureDirectoryExists($diskRoot);

        config()->set('filesystems.disks.public.root', $diskRoot);

        return $diskRoot;
    }
}
