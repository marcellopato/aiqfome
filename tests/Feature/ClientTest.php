<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Client::factory()->createOne();
        $this->actingAs($this->client, 'sanctum');
    }

    public function test_criar_cliente_valido()
    {
        $data = [
            'name' => 'Novo Cliente',
            'email' => 'novo@cliente.com',
        ];
        $response = $this->postJson('/api/clients', $data);
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Novo Cliente', 'email' => 'novo@cliente.com']);
        $this->assertDatabaseHas('clients', ['email' => 'novo@cliente.com']);
    }

    public function test_nao_permite_email_duplicado()
    {
        $data = [
            'name' => 'Outro',
            'email' => $this->client->email,
        ];
        $response = $this->postJson('/api/clients', $data);
        $response->assertStatus(422)
            ->assertJsonFragment(['email' => ['O campo email já está em uso.']]);
    }

    public function test_listar_clientes()
    {
        Client::factory()->create(['name' => 'Cliente 2', 'email' => 'c2@teste.com']);
        $response = $this->getJson('/api/clients');
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $this->client->name])
            ->assertJsonFragment(['name' => 'Cliente 2']);
    }

    public function test_exibir_cliente_especifico()
    {
        $response = $this->getJson("/api/clients/{$this->client->id}");
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $this->client->id, 'email' => $this->client->email]);
    }

    public function test_atualizar_cliente()
    {
        $data = [
            'name' => 'Cliente Atualizado',
            'email' => 'atualizado@teste.com',
        ];
        $response = $this->putJson("/api/clients/{$this->client->id}", $data);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Cliente Atualizado', 'email' => 'atualizado@teste.com']);
        $this->assertDatabaseHas('clients', ['email' => 'atualizado@teste.com']);
    }

    public function test_remover_cliente()
    {
        $response = $this->deleteJson("/api/clients/{$this->client->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('clients', ['id' => $this->client->id]);
    }

    public function test_validacao_campos_obrigatorios()
    {
        $response = $this->postJson('/api/clients', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }
} 