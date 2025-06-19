<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aluno>
 */
class AlunoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => $this->faker->name(),
            'cpf' => $this->generateCpf(),
            'data_nascimento' => $this->faker->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
            'turma' => $this->faker->randomElement(['Turma A', 'Turma B', 'Turma C', 'Turma D']),
            'status' => $this->faker->randomElement(['Pendente', 'Aprovado', 'Cancelado']),
        ];
    }

    private function generateCpf(): string
    {
        $cpf = '';
        for ($i = 0; $i < 9; $i++) {
            $cpf .= $this->faker->numberBetween(0, 9);
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $digit1 = 11 - ($sum % 11);
        if ($digit1 >= 10) $digit1 = 0;

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }
        $sum += $digit1 * 2;
        $digit2 = 11 - ($sum % 11);
        if ($digit2 >= 10) $digit2 = 0;

        $cpf .= $digit1 . $digit2;

        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Pendente',
        ]);
    }

    public function aprovado(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Aprovado',
        ]);
    }

    public function cancelado(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Cancelado',
        ]);
    }
}