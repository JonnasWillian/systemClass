<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Token não fornecido'
            ], 401);
        }

        $user = auth()->user();

        if ($user->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Permissão insuficiente.'
            ], 403);
        }

        return $next($request);
    }
}