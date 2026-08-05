<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully()
    {
        $response = $this->postJson('/api/register', [
            'name'     => 'Jean Client',
            'email'    => 'jean@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['access_token', 'token_type', 'user']);

        $this->assertDatabaseHas('users', [
            'email' => 'jean@example.com',
            'role'  => 'client',
        ]);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type', 'user']);
    }

    public function test_login_rate_limiting_throttles_after_6_attempts()
    {
        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/login', [
                'email'    => 'wrong@example.com',
                'password' => 'wrongpass',
            ]);
        }

        // La 7ème tentative doit être bloquée avec 429 Too Many Requests
        $response = $this->postJson('/api/login', [
            'email'    => 'wrong@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(429);
    }
}
