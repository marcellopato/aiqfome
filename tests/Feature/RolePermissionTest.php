<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles com o guard correto
        Role::findOrCreate('manager', 'web');
        Role::findOrCreate('user', 'web');
    }

    public function test_manager_pode_crud_qualquer_cliente()
    {
        $manager = Client::factory()->create(['email' => 'manager@teste.com']);
        $manager->assignRole('manager');
        $outro = Client::factory()->create(['email' => 'outro@teste.com']);

        $this->actingAs($manager, 'sanctum');

        // Listar
        $this->getJson('/api/clients')->assertStatus(200);
        // Criar
        $this->postJson('/api/clients', ['name' => 'Novo', 'email' => 'novo@teste.com'])->assertStatus(201);
        // Editar outro
        $this->putJson("/api/clients/{$outro->id}", ['name' => 'Editado', 'email' => 'editado@teste.com'])->assertStatus(200);
        // Remover outro
        $this->deleteJson("/api/clients/{$outro->id}")->assertStatus(204);
    }

    public function test_user_so_pode_acessar_editar_remover_proprio_registro()
    {
        $user = Client::factory()->create(['email' => 'user@teste.com']);
        $user->assignRole('user');
        $outro = Client::factory()->create(['email' => 'outro@teste.com']);

        $this->actingAs($user, 'sanctum');

        // Pode ver a si mesmo
        $this->getJson("/api/clients/{$user->id}")->assertStatus(200);
        // Pode editar a si mesmo
        $this->putJson("/api/clients/{$user->id}", ['name' => 'Editado', 'email' => 'user@teste.com'])->assertStatus(200);
        // Pode remover a si mesmo
        $this->deleteJson("/api/clients/{$user->id}")->assertStatus(204);

        // Não pode ver outro
        $this->getJson("/api/clients/{$outro->id}")->assertStatus(403);
        // Não pode editar outro
        $this->putJson("/api/clients/{$outro->id}", ['name' => 'Editado', 'email' => 'outro@teste.com'])->assertStatus(403);
        // Não pode remover outro
        $this->deleteJson("/api/clients/{$outro->id}")->assertStatus(403);
    }
} 