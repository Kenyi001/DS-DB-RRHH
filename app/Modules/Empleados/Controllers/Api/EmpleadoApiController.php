<?php

namespace App\Modules\Empleados\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Empleados\Services\EmpleadoService;
use App\Modules\Empleados\Requests\StoreEmpleadoRequest;
use App\Modules\Empleados\Requests\UpdateEmpleadoRequest;
use App\Modules\Empleados\Resources\EmpleadoResourceAprobado as EmpleadoResource;
use App\Modules\Empleados\Resources\EmpleadoCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class EmpleadoApiController extends Controller
{
    protected $empleadoService;

    public function __construct(EmpleadoService $empleadoService)
    {
        $this->empleadoService = $empleadoService;
        
        // TODO: Implementar middleware de API authentication
        // $this->middleware('auth:sanctum');
        // $this->middleware('throttle:api');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/empleados",
     *     summary="Listar empleados",
     *     tags={"Empleados"},
     *     @OA\Parameter(name="buscar", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="estado", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Lista de empleados")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only([
                'buscar', 'estado', 'genero', 'ciudad',
                'fecha_ingreso_desde', 'fecha_ingreso_hasta',
                'order_by', 'order_direction'
            ]);

            $perPage = $request->get('per_page', 15);
            
            $empleados = $this->empleadoService->listar($filtros, $perPage);

            return response()->json([
                'success' => true,
                'data' => new EmpleadoCollection($empleados),
                'meta' => [
                    'current_page' => $empleados->currentPage(),
                    'per_page' => $empleados->perPage(),
                    'total' => $empleados->total(),
                    'last_page' => $empleados->lastPage(),
                    'filtros_aplicados' => $filtros
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener empleados',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/empleados",
     *     summary="Crear empleado",
     *     tags={"Empleados"},
     *     @OA\Response(response=201, description="Empleado creado exitosamente")
     * )
     */
    public function store(StoreEmpleadoRequest $request): JsonResponse
    {
        try {
            $empleado = $this->empleadoService->crear($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Empleado creado exitosamente',
                'data' => new EmpleadoResource($empleado)
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear empleado',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/empleados/{id}",
     *     summary="Obtener empleado por ID",
     *     tags={"Empleados"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Empleado encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $empleado = $this->empleadoService->obtenerPorId($id);

            return response()->json([
                'success' => true,
                'data' => new EmpleadoResource($empleado)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener empleado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/empleados/{id}",
     *     summary="Actualizar empleado",
     *     tags={"Empleados"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Empleado actualizado")
     * )
     */
    public function update(UpdateEmpleadoRequest $request, int $id): JsonResponse
    {
        try {
            $actualizado = $this->empleadoService->actualizar($id, $request->validated());

            if ($actualizado) {
                $empleado = $this->empleadoService->obtenerPorId($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Empleado actualizado exitosamente',
                    'data' => new EmpleadoResource($empleado)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar el empleado'
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar empleado',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/empleados/{id}",
     *     summary="Eliminar empleado (baja lógica)",
     *     tags={"Empleados"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Empleado eliminado")
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $motivo = $request->get('motivo', 'Baja solicitada desde API');
            $usuario = $request->user()->name ?? 'API'; // TODO: Implementar auth

            $eliminado = $this->empleadoService->eliminar($id, $motivo, $usuario);

            if ($eliminado) {
                return response()->json([
                    'success' => true,
                    'message' => 'Empleado marcado como baja exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el empleado'
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar empleado',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/empleados/{id}/reactivar",
     *     summary="Reactivar empleado",
     *     tags={"Empleados"}
     * )
     */
    public function reactivar(int $id): JsonResponse
    {
        try {
            $reactivado = $this->empleadoService->reactivar($id);

            if ($reactivado) {
                $empleado = $this->empleadoService->obtenerPorId($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Empleado reactivado exitosamente',
                    'data' => new EmpleadoResource($empleado)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo reactivar el empleado'
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar empleado',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/empleados/estadisticas",
     *     summary="Obtener estadísticas de empleados",
     *     tags={"Empleados"}
     * )
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $estadisticas = $this->empleadoService->obtenerEstadisticas();

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/empleados/search",
     *     summary="Buscar empleados para selects",
     *     tags={"Empleados"}
     * )
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $termino = $request->get('q', '');
            $limite = $request->get('limit', 10);

            $empleados = $this->empleadoService->buscarParaSelect($termino, $limite);

            return response()->json([
                'success' => true,
                'data' => $empleados->map(function ($empleado) {
                    return [
                        'id' => $empleado->id,
                        'text' => "{$empleado->nombre_completo} - CI: {$empleado->ci}",
                        'codigo_empleado' => $empleado->codigo_empleado,
                        'ci' => $empleado->ci,
                        'nombre_completo' => $empleado->nombre_completo
                    ];
                })
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/empleados/activos",
     *     summary="Obtener solo empleados activos",
     *     tags={"Empleados"}
     * )
     */
    public function activos(): JsonResponse
    {
        try {
            $empleados = $this->empleadoService->obtenerActivos();

            return response()->json([
                'success' => true,
                'data' => EmpleadoResource::collection($empleados),
                'total' => $empleados->count()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener empleados activos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}