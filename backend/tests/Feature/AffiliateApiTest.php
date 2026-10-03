<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AffiliateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_affiliate_dashboard_data(): void
    {
        $user = User::create([
            'name'           => 'Parrain Pro',
            'email'          => 'parrain@example.com',
            'password'       => bcrypt('Password123!'),
            'affiliate_code' => 'YASS-REF-PARRAIN',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user/affiliate');

        $response->assertStatus(200);
        $response->assertJsonFragment(['affiliate_code' => 'YASS-REF-PARRAIN']);
    }

    public function test_user_can_request_payout(): void
    {
        $user = User::create([
            'name'              => 'Affilié Top',
            'email'             => 'affilie@example.com',
            'password'          => bcrypt('Password123!'),
            'affiliate_balance' => 50.00,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/user/affiliate/payout', [
                'amount'          => 20.00,
                'payment_method'  => 'wave',
                'payment_details' => '+225 07 01 02 03 04',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('affiliate_payouts', [
            'user_id'        => $user->id,
            'amount'         => 20.00,
            'payment_method' => 'wave',
        ]);
    }
}
