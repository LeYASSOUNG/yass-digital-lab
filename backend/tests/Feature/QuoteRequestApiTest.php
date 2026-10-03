<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\QuoteRequest;
use App\Models\User;

class QuoteRequestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_quote_request(): void
    {
        $response = $this->postJson('/api/quote-requests', [
            'name'          => 'Client VIP',
            'email'         => 'vip@example.com',
            'phone'         => '+225 07 00 00 00 00',
            'company'       => 'SaaS Enterprise',
            'service_title' => 'Développement SaaS Vue 3 & Laravel',
            'budget'        => '2 000 000 - 5 000 000 FCFA',
            'deadline'      => '1 Mois',
            'details'       => 'Création d\'une plateforme de gestion.',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('quote_requests', [
            'email'  => 'vip@example.com',
            'budget' => '2 000 000 - 5 000 000 FCFA',
        ]);
    }

    public function test_can_generate_quote_pdf_link(): void
    {
        $quote = QuoteRequest::create([
            'name'          => 'Projet Enterprise',
            'email'         => 'enterprise@example.com',
            'service_title' => 'Agent IA & Automatisation',
            'details'       => 'Intégration d\'agent conversationnel.',
        ]);

        $response = $this->getJson("/api/quote-requests/{$quote->id}/pdf-link");

        $response->assertStatus(200);
        $response->assertJsonStructure(['pdf_url', 'expires_at']);
    }

    public function test_admin_can_update_quote_real_amount(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $quote = QuoteRequest::create([
            'name'          => 'Client Sur Mesure',
            'email'         => 'client@example.com',
            'service_title' => 'Landing Page Pro',
            'budget'        => '500 000 - 2 000 000 FCFA',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/quote-requests/{$quote->id}/status", [
            'amount' => '1 250 000 FCFA',
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('quote_requests', [
            'id'     => $quote->id,
            'amount' => '1 250 000 FCFA',
            'status' => 'in_progress',
        ]);
    }
}
