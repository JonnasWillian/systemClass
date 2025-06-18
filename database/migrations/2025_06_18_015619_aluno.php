<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->string('cpf', 14)->unique();
            $table->date('data_nascimento');
            $table->string('turma');
            $table->enum('status', ['Pendente', 'Aprovado', 'Cancelado'])->default('Pendente');
            $table->timestamps();

            $table->index(['nome']);
            $table->index(['cpf']);
            $table->index(['data_nascimento']);
            $table->index(['turma']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};