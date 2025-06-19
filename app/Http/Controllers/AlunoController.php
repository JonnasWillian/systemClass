<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlunoRequest;
use App\Http\Requests\UpdateAlunoRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Resources\AlunoResource;
use App\Models\Aluno;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlunoController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Aluno::query();

            if ($request->has('nome')) {
                $query->filterByNome($request->nome);
            }

            if ($request->has('cpf')) {
                $query->filterByCpf($request->cpf);
            }

            if ($request->has('data_nascimento')) {
                $query->filterByDataNascimento($request->data_nascimento);
            }

            if ($request->has('turma')) {
                $query->filterByTurma($request->turma);
            }

            if ($request->has('status')) {
                $query->filterByStatus($request->status);
            }

            $perPage = $request->get('per_page', 15);
            $alunos = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => AlunoResource::collection($alunos->items()),
                'pagination' => [
                    'current_page' => $alunos->currentPage(),
                    'last_page' => $alunos->lastPage(),
                    'per_page' => $alunos->perPage(),
                    'total' => $alunos->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar alunos',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor'
            ], 500);
        }
    }

    public function store(StoreAlunoRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $aluno = Aluno::create($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aluno cadastrado com sucesso',
                'data' => new AlunoResource($aluno)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar aluno',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor'
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $aluno = Aluno::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new AlunoResource($aluno)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aluno não encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar aluno',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor'
            ], 500);
        }
    }

    public function update(UpdateAlunoRequest $request, string $id): JsonResponse
    {
        try {
            $aluno = Aluno::findOrFail($id);

            DB::beginTransaction();

            $aluno->update($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aluno atualizado com sucesso',
                'data' => new AlunoResource($aluno->fresh())
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aluno não encontrado'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar aluno',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor'
            ], 500);
        }
    }

    public function updateStatus(UpdateStatusRequest $request, string $id): JsonResponse
    {
        try {
            $aluno = Aluno::findOrFail($id);
            $previousStatus = $aluno->status;
            $newStatus = $request->status;

            if ($newStatus === 'Cancelado' && !$aluno->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível cancelar um aluno que já foi aprovado'
                ], 422);
            }

            DB::beginTransaction();

            $aluno->update(['status' => $newStatus]);

            if ($previousStatus !== $newStatus) {
                $this->notificationService->notifyStatusChange($aluno, $previousStatus);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status do aluno atualizado com sucesso',
                'data' => new AlunoResource($aluno->fresh())
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aluno não encontrado'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status do aluno',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor'
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $aluno = Aluno::findOrFail($id);

            if (!auth()->user()->isGestor()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Apenas gestores podem excluir alunos.'
                ], 403);
            }

            DB::beginTransaction();

            $aluno->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aluno excluído com sucesso'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aluno não encontrado'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir aluno',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor'
            ], 500);
        }
    }
}