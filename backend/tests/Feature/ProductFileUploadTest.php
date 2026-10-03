<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class ProductFileUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product_with_file_upload(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $admin = User::create([
            'name'     => 'Admin Upload',
            'email'    => 'adminupload@example.com',
            'role'     => 'admin',
            'password' => bcrypt('Password123!'),
        ]);

        $category = Category::create(['name' => 'Scripts', 'slug' => 'scripts']);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $zipFile = UploadedFile::fake()->create('package.zip', 500, 'application/zip');
        $imageFile = UploadedFile::fake()->create('thumb.jpg', 100, 'image/jpeg');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/products', [
                'title'       => 'Bot LeadGen Python',
                'category_id' => $category->id,
                'description' => 'Script de prospection automatique.',
                'price'       => 49.99,
                'type'        => 'Outil',
                'file'        => $zipFile,
                'image'       => $imageFile,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['title' => 'Bot LeadGen Python']);
        
        $product = Product::where('title', 'Bot LeadGen Python')->first();
        $this->assertNotNull($product->download_url);
        Storage::disk('local')->assertExists($product->download_url);
    }
}
