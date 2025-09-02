<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        $user = $request->user();

        // Verificar si el usuario está activo
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario inactivo'
            ], 403);
        }

        // Si no se especifican roles, solo verificar que esté autenticado
        if (empty($roles)) {
            return $next($request);
        }

        // Verificar si el usuario tiene uno de los roles requeridos
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // El usuario no tiene el rol requerido
        return response()->json([
            'success' => false,
            'message' => 'No tienes permisos para acceder a este recurso',
            'required_roles' => $roles,
            'user_role' => $user->role
        ], 403);
    }
}