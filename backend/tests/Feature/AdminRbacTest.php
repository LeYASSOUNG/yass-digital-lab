<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class AdminRbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_user_cannot_access_admin_routes()
    {
        $client = User::factory()->create(['role' => 'client']);

        // Le client essaie de créer un produit
        $response = $this->actingAs($client, 'sanctum')->postJson('/api/products', [
            'title'       => 'Hacked Product',
            'category_id' => 1,
            'description' => 'Test',
            'price'       => 99.99,
        ]);

        $response->assertStatus(403);

        // Le client essaie d'accéder à la liste des utilisateurs
        $usersResponse = $this->actingAs($client, 'sanctum')->getJson('/api/users');
        $usersResponse->assertStatus(403);
    }

    public function test_super_admin_can_access_user_management()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin, 'sanctum')->getJson('/api/users');
        $response->assertStatus(200);
    }
}
