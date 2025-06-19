<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAlunoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|size:14|unique:alunos,cpf|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
            'data_nascimento' => 'required|date|date_format:Y-m-d|before:today',
            'turma' => 'required|string|max:255',
            'status' => 'sometimes|in:Pendente,Aprovado,Cancelado',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O campo nome deve ser um texto.',
            'nome.max' => 'O campo nome não pode ter mais de 255 caracteres.',
            
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter exatamente 14 caracteres.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'cpf.regex' => 'O CPF deve estar no formato XXX.XXX.XXX-XX.',
            
            'data_nascimento.required' => 'O campo data de nascimento é obrigatório.',
            'data_nascimento.date' => 'A data de nascimento deve ser uma data válida.',
            'data_nascimento.date_format' => 'A data de nascimento deve estar no formato Y-m-d.',
            'data_nascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
            
            'turma.required' => 'O campo turma é obrigatório.',
            'turma.string' => 'O campo turma deve ser um texto.',
            'turma.max' => 'O campo turma não pode ter mais de 255 caracteres.',
            
            'status.in' => 'O status deve ser: Pendente, Aprovado ou Cancelado.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}