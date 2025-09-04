<?php

namespace App\Modules\Contratos\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Contratos\Services\ContratoService;
use App\Modules\Contratos\Resources\ContratoResource;
use App\Modules\Contratos\Resources\ContratoCollection;
use App\Modules\Contratos\Requests\StoreContratoRequest;
use App\Modules\Contratos\Requests\UpdateContratoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class ContratoApiController extends Controller
{
    protected $contratoService;

    public function __construct(ContratoService $contratoService)
    {
        $this->contratoService = $contratoService;
        
        // TODO: Implementar middleware de API authentication
        // $this->middleware('auth:sanctum');
        // $this->middleware('throttle:api');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/contratos",
     *     summary="Listar contratos",
     *     tags={"Contratos"},
     *     @OA\Parameter(name="buscar", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="estado", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="tipo_contrato", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="departamento_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="solo_vigentes", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Lista de contratos")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only([
                'buscar', 'estado', 'tipo_contrato', 'departamento_id', 'solo_vigentes',
                'fecha_inicio_desde', 'fecha_inicio_hasta',
                'order_by', 'order_direction'
            ]);

            $perPage = $request->get('per_page', 15);
            
            $contratos = $this->contratoService->listar($filtros, $perPage);

            return response()->json([
                'success' => true,
                'data' => new ContratoCollection($contratos),
                'meta' => [
                    'current_page' => $contratos->currentPage(),
                    'per_page' => $contratos->perPage(),
                    'total' => $contratos->total(),
                    'last_page' => $contratos->lastPage(),
                    'filtros_aplicados' => $filtros
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener contratos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/contratos",
     *     summary="Crear contrato",
     *     tags={"Contratos"},
     *     @OA\Response(response=201, description="Contrato creado exitosamente")
     * )
     */
    public function store(StoreContratoRequest $request): JsonResponse
    {
        try {
            $contrato = $this->contratoService->crear($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Contrato creado exitosamente',
                'data' => new ContratoResource($contrato)
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear contrato',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/contratos/{id}",
     *     summary="Obtener contrato por ID",
     *     tags={"Contratos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Contrato encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $contrato = $this->contratoService->obtenerPorId($id);

            return response()->json([
                'success' => true,
                'data' => new ContratoResource($contrato)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener contrato',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/contratos/{id}",
     *     summary="Actualizar contrato",
     *     tags={"Contratos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Contrato actualizado")
     * )
     */
    public function update(UpdateContratoRequest $request, int $id): JsonResponse
    {
        try {
            $actualizado = $this->contratoService->actualizar($id, $request->validated());

            if ($actualizado) {
                $contrato = $this->contratoService->obtenerPorId($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Contrato actualizado exitosamente',
                    'data' => new ContratoResource($contrato)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar el contrato'
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar contrato',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/contratos/{id}",
     *     summary="Desactivar contrato",
     *     tags={"Contratos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Contrato desactivado")
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $motivo = $request->get('motivo', 'Baja de contrato desde API');
            $usuario = $request->user()->name ?? 'API';

            $eliminado = $this->contratoService->eliminar($id, $motivo, $usuario);

            if ($eliminado) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contrato desactivado exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo desactivar el contrato'
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar contrato',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/contratos/{id}/reactivar",
     *     summary="Reactivar contrato",
     *     tags={"Contratos"}
     * )
     */
    public function reactivar(int $id): JsonResponse
    {
        try {
            $reactivado = $this->contratoService->reactivar($id);

            if ($reactivado) {
                $contrato = $this->contratoService->obtenerPorId($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Contrato reactivado exitosamente',
                    'data' => new ContratoResource($contrato)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo reactivar el contrato'
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar contrato',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/contratos/{id}/finalizar",
     *     summary="Finalizar contrato",
     *     tags={"Contratos"}
     * )
     */
    public function finalizar(Request $request, int $id): JsonResponse
    {
        try {
            $fechaFin = $request->get('fecha_fin', now()->toDateString());
            $motivo = $request->get('motivo', '');

            $finalizado = $this->contratoService->finalizarContrato($id, $fechaFin, $motivo);

            if ($finalizado) {
                $contrato = $this->contratoService->obtenerPorId($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Contrato finalizado exitosamente',
                    'data' => new ContratoResource($contrato)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo finalizar el contrato'
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar contrato',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/contratos/{id}/renovar",
     *     summary="Renovar contrato",
     *     tags={"Contratos"}
     * )
     */
    public function renovar(Request $request, int $id): JsonResponse
    {
        try {
            $datosRenovacion = $request->all();
            
            $nuevoContrato = $this->contratoService->renovarContrato($id, $datosRenovacion);

            return response()->json([
                'success' => true,
                'message' => 'Contrato renovado exitosamente',
                'data' => new ContratoResource($nuevoContrato)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al renovar contrato',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/contratos/estadisticas",
     *     summary="Obtener estadísticas de contratos",
     *     tags={"Contratos"}
     * )
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $estadisticas = $this->contratoService->obtenerEstadisticas();

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
     *     path="/api/v1/contratos/vigentes",
     *     summary="Obtener solo contratos vigentes",
     *     tags={"Contratos"}
     * )
     */
    public function vigentes(): JsonResponse
    {
        try {
            $contratos = $this->contratoService->obtenerVigentes();

            return response()->json([
                'success' => true,
                'data' => ContratoResource::collection($contratos),
                'total' => $contratos->count()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener contratos vigentes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/empleados/{empleadoId}/contratos",
     *     summary="Obtener contratos de un empleado",
     *     tags={"Contratos"}
     * )
     */
    public function contratosPorEmpleado(int $empleadoId): JsonResponse
    {
        try {
            $contratos = $this->contratoService->obtenerPorEmpleado($empleadoId);

            return response()->json([
                'success' => true,
                'data' => ContratoResource::collection($contratos),
                'total' => $contratos->count()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener contratos del empleado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/empleados/{empleadoId}/contrato-vigente",
     *     summary="Obtener contrato vigente de un empleado",
     *     tags={"Contratos"}
     * )
     */
    public function contratoVigenteEmpleado(int $empleadoId): JsonResponse
    {
        try {
            $contrato = $this->contratoService->obtenerContratoVigenteEmpleado($empleadoId);

            if ($contrato) {
                return response()->json([
                    'success' => true,
                    'data' => new ContratoResource($contrato)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'El empleado no tiene contrato vigente'
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener contrato vigente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/contratos/search",
     *     summary="Buscar contratos para selects",
     *     tags={"Contratos"}
     * )
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $termino = $request->get('q', '');
            $limite = $request->get('limit', 10);

            $contratos = $this->contratoService->buscarParaSelect($termino, $limite);

            return response()->json([
                'success' => true,
                'data' => $contratos->map(function ($contrato) {
                    return [
                        'id' => $contrato->IDContrato,
                        'text' => "{$contrato->NumeroContrato} - {$contrato->empleado_nombre_completo}",
                        'numero_contrato' => $contrato->NumeroContrato,
                        'empleado_id' => $contrato->IDEmpleado,
                        'empleado_nombre' => $contrato->empleado_nombre_completo,
                        'haber_basico' => $contrato->HaberBasico,
                        'es_vigente' => $contrato->es_vigente
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
}