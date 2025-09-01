<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PlanillaService
{
    public function generar(int $mes, int $gestion, string $usuario): array
    {
        $idempotencyKey = Str::uuid();
        
        Log::info('Iniciando generación de planilla', [
            'mes' => $mes,
            'gestion' => $gestion,
            'usuario' => $usuario,
            'idempotency_key' => $idempotencyKey
        ]);

        try {
            $startTime = microtime(true);

            // Validaciones básicas
            if ($mes < 1 || $mes > 12) {
                throw new \InvalidArgumentException('Mes debe estar entre 1 y 12');
            }

            if ($gestion < 2020 || $gestion > date('Y')) {
                throw new \InvalidArgumentException('Gestión inválida');
            }

            // Verificar si ya existe planilla para el período
            $existente = DB::table('LogPlanilla')
                          ->where('Mes', $mes)
                          ->where('Gestion', $gestion)
                          ->where('EstadoProceso', 'Completado')
                          ->first();

            if ($existente) {
                return [
                    'success' => true,
                    'planilla_id' => $existente->PlanillaId,
                    'message' => 'Planilla ya existe para este período'
                ];
            }

            // Por ahora simulamos la llamada al SP (se implementará cuando esté disponible)
            $planillaId = $this->simularGeneracionPlanilla($mes, $gestion, $usuario, $idempotencyKey);

            $duration = (microtime(true) - $startTime) * 1000;

            Log::info('Planilla generada exitosamente', [
                'planilla_id' => $planillaId,
                'duration_ms' => $duration,
                'contratos_procesados' => $this->contarContratosActivos()
            ]);

            return [
                'success' => true,
                'planilla_id' => $planillaId,
                'message' => "Planilla {$mes}/{$gestion} generada exitosamente"
            ];

        } catch (\Exception $e) {
            Log::error('Error generando planilla', [
                'error' => $e->getMessage(),
                'mes' => $mes,
                'gestion' => $gestion
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function simularGeneracionPlanilla(int $mes, int $gestion, string $usuario, string $idempotencyKey): int
    {
        // Simulación hasta que sp_GenerarPlanillaMensual esté implementado
        $planillaId = rand(1000, 9999);
        
        // En implementación real, esto sería:
        // DB::statement('EXEC sp_GenerarPlanillaMensual ?, ?, ?, ?, ?', [
        //     $mes, $gestion, $usuario, $idempotencyKey, $planillaId
        // ]);

        return $planillaId;
    }

    private function contarContratosActivos(): int
    {
        return DB::table('contratos')
                 ->where('Estado', 'Activo')
                 ->whereRaw('GETDATE() BETWEEN FechaInicio AND ISNULL(FechaFin, GETDATE())')
                 ->count();
    }

    public function getEstadoPlanilla(int $planillaId): ?array
    {
        $planilla = DB::table('LogPlanilla')
                     ->where('PlanillaId', $planillaId)
                     ->first();

        if (!$planilla) {
            return null;
        }

        return [
            'planilla_id' => $planilla->PlanillaId,
            'mes' => $planilla->Mes,
            'gestion' => $planilla->Gestion,
            'estado' => $planilla->EstadoProceso,
            'fecha_inicio' => $planilla->FechaInicio,
            'fecha_fin' => $planilla->FechaFin,
            'contratos_procesados' => $planilla->ContratosProcessados
        ];
    }
}