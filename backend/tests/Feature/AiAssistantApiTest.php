<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;

class AiAssistantApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_recommender_returns_smart_recommendations(): void
    {
        $category = Category::create(['name' => 'Templates', 'slug' => 'templates']);
        Product::create([
            'category_id' => $category->id,
            'title'       => 'Template SaaS Vue 3',
            'slug'        => 'template-saas-vue-3',
            'description' => 'Template SaaS réactif et moderne.',
            'price'       => 49.00,
            'type'        => 'Pack',
        ]);

        $response = $this->postJson('/api/ai/recommend', [
            'message' => 'Je cherche un template saas moderne pour créer une web app.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply', 'recommended_products']);
        $response->assertJsonFragment(['title' => 'Template SaaS Vue 3']);
    }
}
