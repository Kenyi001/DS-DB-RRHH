<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\EmpleadoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    public function __construct(
        private EmpleadoRepository $empleadoRepository
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'departamento_id']);
        $perPage = $request->get('per_page', 15);

        $empleados = $this->empleadoRepository->findWithFilters($filters, $perPage);

        return response()->json([
            'success' => true,
            'code' => 0,
            'data' => $empleados,
            'message' => 'Empleados obtenidos exitosamente'
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'CI' => ['required', 'string', 'regex:/^\d{7,8}$/', 'unique:empleados,CI'],
            'Nombres' => ['required', 'string', 'max:100', 'regex:/^[a-zA-ZÀ-ÿ\s]+$/'],
            'ApellidoPaterno' => ['required', 'string', 'max:50'],
            'ApellidoMaterno' => ['nullable', 'string', 'max:50'],
            'FechaNacimiento' => ['required', 'date', 'before:18 years ago'],
            'Email' => ['required', 'email:rfc,dns', 'max:150', 'unique:empleados,Email'],
            'Telefono' => ['nullable', 'regex:/^\+?591[0-9]{8}$/']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => -1,
                'error' => 'Datos de entrada inválidos',
                'details' => $validator->errors()
            ], 400);
        }

        try {
            $data = $validator->validated();
            $data['UsuarioCreacion'] = auth()->user()->name ?? 'API_USER';
            
            $empleado = $this->empleadoRepository->create($data);

            return response()->json([
                'success' => true,
                'code' => 0,
                'data' => $empleado,
                'message' => 'Empleado creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => -99,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $empleado = $this->empleadoRepository->findById($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'code' => -2,
                'error' => 'Empleado no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code' => 0,
            'data' => $empleado,
            'message' => 'Empleado obtenido exitosamente'
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $empleado = $this->empleadoRepository->findById($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'code' => -2,
                'error' => 'Empleado no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'CI' => ['required', 'string', 'regex:/^\d{7,8}$/', 'unique:empleados,CI,' . $id . ',IDEmpleado'],
            'Nombres' => ['required', 'string', 'max:100'],
            'ApellidoPaterno' => ['required', 'string', 'max:50'],
            'Email' => ['required', 'email', 'unique:empleados,Email,' . $id . ',IDEmpleado'],
            'Telefono' => ['nullable', 'regex:/^\+?591[0-9]{8}$/']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => -1,
                'error' => 'Datos de entrada inválidos',
                'details' => $validator->errors()
            ], 400);
        }

        try {
            $data = $validator->validated();
            $data['UsuarioModificacion'] = auth()->user()->name ?? 'API_USER';
            $data['FechaModificacion'] = now();

            $this->empleadoRepository->update($id, $data);

            return response()->json([
                'success' => true,
                'code' => 0,
                'message' => 'Empleado actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => -99,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $empleado = $this->empleadoRepository->findById($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'code' => -2,
                'error' => 'Empleado no encontrado'
            ], 404);
        }

        try {
            $usuario = auth()->user()->name ?? 'API_USER';
            $this->empleadoRepository->softDelete($id, $usuario);

            return response()->json([
                'success' => true,
                'code' => 0,
                'message' => 'Empleado eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => -99,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }
}