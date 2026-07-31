<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Vérifie que la page d'accueil retourne un statut HTTP 200.
     */
    public function testTheApplicationReturnsASuccessfulResponse(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
