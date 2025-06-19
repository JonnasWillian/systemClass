<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateAlunoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $alunoId = $this->route('aluno');

        return [
            'nome' => 'sometimes|required|string|max:255',
            'cpf' => [
                'sometimes',
                'required',
                'string',
                'size:14',
                'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
                Rule::unique('alunos', 'cpf')->ignore($alunoId),
            ],
            'data_nascimento' => 'sometimes|required|date|date_format:Y-m-d|before:today',
            'turma' => 'sometimes|required|string|max:255',
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