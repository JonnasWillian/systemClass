<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Gestor Admin',
            'email' => 'gestor@teste.com',
            'password' => Hash::make('123456'),
            'role' => 'gestor',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Funcionário Teste',
            'email' => 'funcionario@teste.com',
            'password' => Hash::make('123456'),
            'role' => 'funcionario',
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@teste.com',
            'role' => 'gestor',
        ]);

        User::factory()->create([
            'name' => 'Maria Santos',
            'email' => 'maria@teste.com',
            'role' => 'funcionario',
        ]);
    }
}