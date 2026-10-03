<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AnalyticsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_fetch_analytics_kpis(): void
    {
        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@yassdigitallab.com',
            'role'     => 'super_admin',
            'password' => bcrypt('Password123!'),
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/analytics');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'kpis' => ['total_revenue', 'average_order_value'],
            'top_products',
        ]);
    }
}
