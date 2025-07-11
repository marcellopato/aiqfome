<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        // Cria um cliente para os testes
        $this->client = Client::factory()->createOne();
        // Autentica como usuário (ajuste conforme sua autenticação)
        $this->actingAs($this->client, 'sanctum');
    }

    public function test_adicionar_favorito_valido()
    {
        Http::fake([
            'fakestoreapi.com/products/1' => Http::response([
                'id' => 1,
                'title' => 'Produto Teste',
                'image' => 'img.jpg',
                'price' => 10.0,
                'rating' => ['rate' => 4, 'count' => 10],
            ], 200),
        ]);

        $response = $this->postJson("/api/clients/{$this->client->id}/favorites", [
            'product_id' => 1
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['id' => 1, 'title' => 'Produto Teste']);
        $this->assertDatabaseHas('favorites', [
            'client_id' => $this->client->id,
            'product_id' => 1,
        ]);
    }

    public function test_nao_permite_favorito_duplicado()
    {
        Favorite::create(['client_id' => $this->client->id, 'product_id' => 1]);
        Http::fake([
            'fakestoreapi.com/products/1' => Http::response([
                'id' => 1,
                'title' => 'Produto Teste',
                'image' => 'img.jpg',
                'price' => 10.0,
                'rating' => ['rate' => 4, 'count' => 10],
            ], 200),
        ]);
        $response = $this->postJson("/api/clients/{$this->client->id}/favorites", [
            'product_id' => 1
        ]);
        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Produto já favoritado.']);
    }

    public function test_nao_adiciona_favorito_invalido()
    {
        Http::fake([
            'fakestoreapi.com/products/9999' => Http::response([], 200), // Sem campos obrigatórios
        ]);
        $response = $this->postJson("/api/clients/{$this->client->id}/favorites", [
            'product_id' => 9999
        ]);
        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Produto não encontrado na API externa.']);
    }

    public function test_listar_favoritos_retorna_apenas_validos()
    {
        Favorite::create(['client_id' => $this->client->id, 'product_id' => 1]);
        Favorite::create(['client_id' => $this->client->id, 'product_id' => 2]);
        Http::fake([
            'fakestoreapi.com/products/1' => Http::response([
                'id' => 1,
                'title' => 'Produto Teste',
                'image' => 'img.jpg',
                'price' => 10.0,
                'rating' => ['rate' => 4, 'count' => 10],
            ], 200),
            'fakestoreapi.com/products/2' => Http::response([], 200), // Inválido
        ]);
        $response = $this->getJson("/api/clients/{$this->client->id}/favorites");
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => 1, 'title' => 'Produto Teste'])
            ->assertJsonMissing(['id' => 2]);
    }

    public function test_remover_favorito()
    {
        $fav = Favorite::create(['client_id' => $this->client->id, 'product_id' => 1]);
        $response = $this->deleteJson("/api/clients/{$this->client->id}/favorites/1");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('favorites', [
            'id' => $fav->id
        ]);
    }
} 