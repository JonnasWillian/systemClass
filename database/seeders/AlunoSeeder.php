<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aluno;

class AlunoSeeder extends Seeder
{
    public function run(): void
    {
        Aluno::factory()->count(5)->pendente()->create();
        Aluno::factory()->count(3)->aprovado()->create();
        Aluno::factory()->count(2)->cancelado()->create();

        Aluno::create([
            'nome' => 'Ana Silva',
            'cpf' => '123.456.789-01',
            'data_nascimento' => '2000-05-15',
            'turma' => 'Turma A',
            'status' => 'Pendente',
        ]);

        Aluno::create([
            'nome' => 'Carlos Oliveira',
            'cpf' => '987.654.321-00',
            'data_nascimento' => '1999-08-22',
            'turma' => 'Turma B',
            'status' => 'Aprovado',
        ]);

        Aluno::create([
            'nome' => 'Mariana Costa',
            'cpf' => '456.789.123-45',
            'data_nascimento' => '2001-12-10',
            'turma' => 'Turma C',
            'status' => 'Cancelado',
        ]);
    }
}