<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Aluno extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nome',
        'cpf',
        'data_nascimento',
        'turma',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    public function scopeFilterByNome($query, $nome)
    {
        return $query->where('nome', 'like', '%' . $nome . '%');
    }

    public function scopeFilterByCpf($query, $cpf)
    {
        return $query->where('cpf', $cpf);
    }

    public function scopeFilterByDataNascimento($query, $data)
    {
        return $query->whereDate('data_nascimento', $data);
    }

    public function scopeFilterByTurma($query, $turma)
    {
        return $query->where('turma', 'like', '%' . $turma . '%');
    }

    public function scopeFilterByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function canBeCancelled(): bool
    {
        return $this->status !== 'Aprovado';
    }

    public function getPreviousStatus(): ?string
    {
        return $this->getOriginal('status');
    }

    public function wasStatusChanged(): bool
    {
        return $this->isDirty('status');
    }
}