<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlanillaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanillaController extends Controller
{
    public function __construct(
        private PlanillaService $planillaService
    ) {
        $this->middleware('auth:sanctum');
    }

    public function generar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mes' => ['required', 'integer', 'between:1,12'],
            'gestion' => ['required', 'integer', 'min:2020', 'max:' . (date('Y') + 1)],
            'idempotency_key' => ['nullable', 'string', 'uuid']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => -1,
                'error' => 'Parámetros inválidos',
                'details' => $validator->errors()
            ], 400);
        }

        $data = $validator->validated();
        $usuario = auth()->user()->name ?? 'API_USER';

        try {
            $resultado = $this->planillaService->generar(
                $data['mes'], 
                $data['gestion'], 
                $usuario
            );

            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'code' => -99,
                    'error' => $resultado['error']
                ], 500);
            }

            // Respuesta 202 Accepted para operaciones asíncronas
            return response()->json([
                'success' => true,
                'code' => 0,
                'data' => [
                    'planilla_id' => $resultado['planilla_id'],
                    'status' => 'Iniciado',
                    'estimated_duration' => '25s'
                ],
                'message' => $resultado['message'],
                'links' => [
                    'status_url' => "/api/v1/planilla/status/{$resultado['planilla_id']}"
                ]
            ], 202);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => -99,
                'error' => 'Error interno procesando planilla'
            ], 500);
        }
    }

    public function status(int $planillaId): JsonResponse
    {
        $estado = $this->planillaService->getEstadoPlanilla($planillaId);

        if (!$estado) {
            return response()->json([
                'success' => false,
                'code' => -2,
                'error' => 'Planilla no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code' => 0,
            'data' => $estado,
            'message' => 'Estado de planilla obtenido'
        ]);
    }

    public function preview(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mes' => ['required', 'integer', 'between:1,12'],
            'gestion' => ['required', 'integer', 'min:2020']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => -1,
                'error' => 'Parámetros inválidos',
                'details' => $validator->errors()
            ], 400);
        }

        // Preview básico - expandir cuando GestionSalarios tenga datos
        $preview = [
            'mes' => $request->mes,
            'gestion' => $request->gestion,
            'total_empleados' => 10, // Placeholder
            'total_liquido' => 85000.00, // Placeholder
            'fecha_generacion' => now()->toDateString()
        ];

        return response()->json([
            'success' => true,
            'code' => 0,
            'data' => $preview,
            'message' => 'Preview de planilla generado'
        ]);
    }
}