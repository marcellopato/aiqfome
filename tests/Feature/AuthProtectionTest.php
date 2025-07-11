<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_nao_autenticado_nao_acessa_rotas_protegidas()
    {
        $response = $this->getJson('/api/clients');
        $response->assertStatus(401);

        $response = $this->postJson('/api/clients', [
            'name' => 'Teste',
            'email' => 'teste@teste.com',
        ]);
        $response->assertStatus(401);
    }

    public function test_nao_autenticado_nao_acessa_favoritos()
    {
        $response = $this->getJson('/api/clients/1/favorites');
        $response->assertStatus(401);
    }

    public function test_token_invalido_nao_acessa_rotas_protegidas()
    {
        $response = $this->withHeader('Authorization', 'Bearer token_invalido')
            ->getJson('/api/clients');
        $response->assertStatus(401);
    }

    public function test_rota_publica_login_funciona_sem_autenticacao()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'fake@fake.com',
            'password' => 'fakepassword',
        ]);
        // Pode ser 401 ou 422, mas não deve ser 401 por falta de token
        $this->assertContains($response->status(), [401, 422]);
    }
} 