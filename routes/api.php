<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlunoController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::group([
    'middleware' => ['auth:api']
], function () {
    Route::apiResource('alunos', AlunoController::class)->except(['destroy']);

    Route::group(['middleware' => 'role:gestor'], function () {
        Route::patch('alunos/{aluno}/status', [AlunoController::class, 'updateStatus']);

        Route::delete('alunos/{aluno}', [AlunoController::class, 'destroy']);
    });
});

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando corretamente',
        'timestamp' => now()
    ]);
});