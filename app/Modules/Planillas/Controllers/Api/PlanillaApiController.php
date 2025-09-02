<?php

namespace App\Modules\Planillas\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Planillas\Services\PlanillaService;
use App\Modules\Planillas\Resources\PlanillaResource;
use App\Modules\Planillas\Resources\PlanillaCollection;
use App\Modules\Planillas\Requests\GenerarPlanillaRequest;
use App\Modules\Planillas\Requests\UpdatePlanillaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class PlanillaApiController extends Controller
{
    protected $planillaService;

    public function __construct(PlanillaService $planillaService)
    {
        $this->planillaService = $planillaService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/planillas",
     *     summary="Listar planillas",
     *     tags={"Planillas"},
     *     @OA\Parameter(name="gestion", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="mes", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="estado", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Lista de planillas")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only([
                'gestion', 'mes', 'estado', 'empleado_id', 'departamento_id'
            ]);

            $perPage = $request->get('per_page', 15);
            
            $planillas = $this->planillaService->listar($filtros, $perPage);

            return response()->json([
                'success' => true,
                'data' => new PlanillaCollection($planillas),
                'meta' => [
                    'current_page' => $planillas->currentPage(),
                    'per_page' => $planillas->perPage(),
                    'total' => $planillas->total(),
                    'last_page' => $planillas->lastPage(),
                    'filtros_aplicados' => $filtros
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener planillas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/planillas/generar",
     *     summary="Generar planilla para período específico",
     *     tags={"Planillas"}
     * )
     */
    public function generar(GenerarPlanillaRequest $request): JsonResponse
    {
        try {
            $datos = $request->validated();
            
            $planillas = $this->planillaService->generarPlanillaPorPeriodo(
                $datos['gestion'],
                $datos['mes'],
                $datos['empleados'] ?? [],
                $datos['opciones'] ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Planillas generadas exitosamente',
                'data' => [
                    'total_generadas' => count($planillas),
                    'periodo' => "{$datos['mes']}/{$datos['gestion']}",
                    'planillas' => PlanillaResource::collection($planillas)
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar planillas',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/planillas/{id}",
     *     summary="Obtener planilla por ID",
     *     tags={"Planillas"}
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $planilla = $this->planillaService->obtenerPorId($id);

            return response()->json([
                'success' => true,
                'data' => new PlanillaResource($planilla)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener planilla',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/planillas/{id}",
     *     summary="Actualizar planilla",
     *     tags={"Planillas"}
     * )
     */
    public function update(UpdatePlanillaRequest $request, int $id): JsonResponse
    {
        try {
            $actualizado = $this->planillaService->actualizar($id, $request->validated());

            if ($actualizado) {
                $planilla = $this->planillaService->obtenerPorId($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Planilla actualizada exitosamente',
                    'data' => new PlanillaResource($planilla)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar la planilla'
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar planilla',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/planillas/{id}/pagar",
     *     summary="Marcar planilla como pagada",
     *     tags={"Planillas"}
     * )
     */
    public function marcarComoPagada(Request $request, int $id): JsonResponse
    {
        try {
            $fechaPago = $request->get('fecha_pago', now()->toDateString());
            
            $pagada = $this->planillaService->marcarComoPagada($id, $fechaPago);

            if ($pagada) {
                $planilla = $this->planillaService->obtenerPorId($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Planilla marcada como pagada',
                    'data' => new PlanillaResource($planilla)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo procesar el pago'
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar pago',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/planillas/{id}/anular",
     *     summary="Anular planilla",
     *     tags={"Planillas"}
     * )
     */
    public function anular(Request $request, int $id): JsonResponse
    {
        try {
            $motivo = $request->get('motivo', 'Anulación solicitada');
            
            $anulada = $this->planillaService->anular($id, $motivo);

            if ($anulada) {
                $planilla = $this->planillaService->obtenerPorId($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Planilla anulada exitosamente',
                    'data' => new PlanillaResource($planilla)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo anular la planilla'
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al anular planilla',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/planillas/periodo/{gestion}/{mes}",
     *     summary="Obtener planillas por período",
     *     tags={"Planillas"}
     * )
     */
    public function porPeriodo(int $gestion, int $mes): JsonResponse
    {
        try {
            $planillas = $this->planillaService->obtenerPorPeriodo($gestion, $mes);

            return response()->json([
                'success' => true,
                'data' => PlanillaResource::collection($planillas),
                'periodo' => [
                    'gestion' => $gestion,
                    'mes' => $mes,
                    'periodo_texto' => $planillas->first()?->periodo_texto ?? "{$mes}/{$gestion}",
                    'total_planillas' => $planillas->count()
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener planillas del período',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/planillas/estadisticas",
     *     summary="Obtener estadísticas de planillas",
     *     tags={"Planillas"}
     * )
     */
    public function estadisticas(Request $request): JsonResponse
    {
        try {
            $gestion = $request->get('gestion', now()->year);
            $mes = $request->get('mes');
            
            $estadisticas = $this->planillaService->obtenerEstadisticas($gestion, $mes);

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
     *     path="/api/v1/planillas/reporte/{gestion}/{mes}",
     *     summary="Generar reporte de planilla por período",
     *     tags={"Planillas"}
     * )
     */
    public function reporte(int $gestion, int $mes, Request $request): JsonResponse
    {
        try {
            $formato = $request->get('formato', 'json');
            $filtros = $request->only(['departamento_id', 'cargo_id', 'estado']);
            
            $reporte = $this->planillaService->generarReporte($gestion, $mes, $filtros, $formato);

            return response()->json([
                'success' => true,
                'data' => $reporte,
                'meta' => [
                    'periodo' => "{$mes}/{$gestion}",
                    'formato' => $formato,
                    'generado_en' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/empleados/{empleadoId}/planillas",
     *     summary="Obtener historial de planillas de un empleado",
     *     tags={"Planillas"}
     * )
     */
    public function planillasEmpleado(int $empleadoId, Request $request): JsonResponse
    {
        try {
            $gestion = $request->get('gestion');
            $limite = $request->get('limite', 12);
            
            $planillas = $this->planillaService->obtenerPorEmpleado($empleadoId, $gestion, $limite);

            return response()->json([
                'success' => true,
                'data' => PlanillaResource::collection($planillas),
                'total' => $planillas->count()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener planillas del empleado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}