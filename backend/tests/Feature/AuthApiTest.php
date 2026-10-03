<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Alice Traoré',
            'email'                 => 'alice@example.com',
            'password'              => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['access_token', 'user']);
        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name'     => 'Bob Kone',
            'email'    => 'bob@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'bob@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'user']);
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::create([
            'name'     => 'Marc Ouattara',
            'email'    => 'marc@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/user/profile', [
                'name'    => 'Marc Ouattara Modifié',
                'company' => 'Tech Lab CI',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id'      => $user->id,
            'name'    => 'Marc Ouattara Modifié',
            'company' => 'Tech Lab CI',
        ]);
    }
}
