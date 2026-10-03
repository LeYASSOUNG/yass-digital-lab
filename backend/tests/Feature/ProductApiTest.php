<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_products_list(): void
    {
        $category = Category::create(['name' => 'Templates Web', 'slug' => 'templates-web']);
        Product::create([
            'category_id' => $category->id,
            'title'       => 'Template SaaS Vue 3',
            'slug'        => 'template-saas-vue-3',
            'description' => 'Un template moderne et réactif.',
            'price'       => 49.00,
            'type'        => 'Pack',
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Template SaaS Vue 3']);
    }

    public function test_can_filter_products_by_search(): void
    {
        $category = Category::create(['name' => 'Prompts IA', 'slug' => 'prompts-ia']);
        Product::create([
            'category_id' => $category->id,
            'title'       => 'Pack Prompts ChatGPT Marketing',
            'slug'        => 'pack-prompts-chatgpt',
            'description' => 'Prompts optimisés pour booster vos ventes.',
            'price'       => 19.00,
            'type'        => 'Prompts',
        ]);

        $response = $this->getJson('/api/products?search=Marketing');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Pack Prompts ChatGPT Marketing']);
    }

    public function test_can_submit_product_review(): void
    {
        $category = Category::create(['name' => 'Scripts', 'slug' => 'scripts']);
        $product = Product::create([
            'category_id' => $category->id,
            'title'       => 'Script Automation Python',
            'slug'        => 'script-automation-python',
            'description' => 'Automation complète.',
            'price'       => 29.00,
            'type'        => 'Script',
        ]);

        $response = $this->postJson("/api/products/{$product->id}/reviews", [
            'name'    => 'Jean Dupont',
            'rating'  => 5,
            'comment' => 'Excellent script, très rapide !',
            'email'   => 'jean@example.com',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Votre avis a été publié avec succès !']);
        $this->assertDatabaseHas('reviews', ['product_id' => $product->id, 'rating' => 5]);
    }
}
