<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de usuario
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'empleado_id' => 'nullable|exists:empleados,IDEmpleado',
            'role' => 'nullable|in:admin,manager,user',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'empleado_id' => $request->empleado_id,
                'role' => $request->role ?? 'user',
            ]);

            $token = $user->createApiToken();

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'empleado_id' => $user->empleado_id,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Iniciar sesión
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar si el usuario está bloqueado
            if ($user->isLocked()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario bloqueado por múltiples intentos fallidos',
                    'locked_until' => $user->locked_until->format('Y-m-d H:i:s')
                ], 423);
            }

            // Verificar si el usuario está activo
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario inactivo. Contacte al administrador.'
                ], 403);
            }

            // Verificar credenciales
            if (!Hash::check($request->password, $user->password)) {
                $user->incrementLoginAttempts();
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas',
                    'attempts_remaining' => max(0, 5 - $user->fresh()->login_attempts)
                ], 401);
            }

            // Login exitoso - resetear intentos
            $user->resetLoginAttempts();

            // Actualizar último login
            $user->updateLastLogin($request->ip());

            $token = $user->createApiToken();

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'full_name' => $user->full_name,
                        'avatar_url' => $user->avatar_url,
                        'theme' => $user->theme,
                        'language' => $user->language,
                        'timezone' => $user->timezone,
                        'last_login_at' => $user->last_login_at?->format('Y-m-d H:i:s'),
                        'empleado_id' => $user->empleado_id,
                        'empleado' => $user->empleado ? [
                            'IDEmpleado' => $user->empleado->IDEmpleado,
                            'Nombres' => $user->empleado->Nombres,
                            'ApellidoPaterno' => $user->empleado->ApellidoPaterno,
                            'ApellidoMaterno' => $user->empleado->ApellidoMaterno,
                            'Email' => $user->empleado->Email,
                            'Telefono' => $user->empleado->Telefono,
                        ] : null
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'preferences' => $user->preferences
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout exitoso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perfil del usuario
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'empleado_id' => $user->empleado_id,
                    'is_active' => $user->is_active,
                    'last_login_at' => $user->last_login_at,
                    'empleado' => $user->empleado ? [
                        'nombres' => $user->empleado->nombres,
                        'apellido_paterno' => $user->empleado->apellido_paterno,
                        'apellido_materno' => $user->empleado->apellido_materno,
                        'nombre_completo' => $user->empleado->nombre_completo,
                        'codigo_empleado' => $user->empleado->codigo_empleado,
                        'email' => $user->empleado->email,
                        'estado' => $user->empleado->estado,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña actual incorrecta'
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar contraseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}