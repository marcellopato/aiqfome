<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criação das roles iniciais
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);

        // Usuário comum
        $user = User::firstOrCreate([
            'email' => 'user@teste.com',
        ], [
            'name' => 'Usuário Teste',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($userRole);

        // Manager
        $manager = User::firstOrCreate([
            'email' => 'manager@teste.com',
        ], [
            'name' => 'Manager Teste',
            'password' => bcrypt('password'),
        ]);
        $manager->assignRole($managerRole);
    }
}
