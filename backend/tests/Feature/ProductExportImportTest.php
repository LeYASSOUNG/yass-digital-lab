<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class ProductExportImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_products_as_csv(): void
    {
        $admin = User::create([
            'name'     => 'Admin Export',
            'email'    => 'exportadmin@example.com',
            'role'     => 'admin',
            'password' => bcrypt('Password123!'),
        ]);

        $category = Category::create(['name' => 'Web', 'slug' => 'web']);
        Product::create([
            'category_id' => $category->id,
            'title'       => 'Produit Test Export',
            'slug'        => 'produit-test-export',
            'description' => 'Description test',
            'price'       => 29.99,
            'type'        => 'Pack',
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->get('/api/admin/products/export?format=csv');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_export_products_as_json(): void
    {
        $admin = User::create([
            'name'     => 'Admin Export JSON',
            'email'    => 'exportjson@example.com',
            'role'     => 'admin',
            'password' => bcrypt('Password123!'),
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->get('/api/admin/products/export?format=json');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    public function test_admin_can_import_products_from_json_file(): void
    {
        $admin = User::create([
            'name'     => 'Admin Import',
            'email'    => 'importadmin@example.com',
            'role'     => 'admin',
            'password' => bcrypt('Password123!'),
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $jsonContent = json_encode([
            [
                'title'       => 'Nouveau Pack Importé',
                'price'       => 59.00,
                'type'        => 'Template',
                'description' => 'Un super template importé via JSON.',
            ]
        ]);

        $file = UploadedFile::fake()->createWithContent('import.json', $jsonContent);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/admin/products/import', [
                'file' => $file,
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['imported_count' => 1]);
        $this->assertDatabaseHas('products', ['title' => 'Nouveau Pack Importé']);
    }
}
