<?php

namespace App\Modules\Planillas\Services;

use App\Models\Planilla;
use App\Models\Contrato;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class PlanillaService
{
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Planilla::with(['contrato.empleado']);

        // Aplicar filtros
        if (!empty($filtros['search'])) {
            $query->whereHas('contrato.empleado', function ($q) use ($filtros) {
                $search = $filtros['search'];
                $q->where(function ($sq) use ($search) {
                    $sq->where('Nombres', 'like', "%{$search}%")
                       ->orWhere('ApellidoPaterno', 'like', "%{$search}%")
                       ->orWhere('ApellidoMaterno', 'like', "%{$search}%")
                       ->orWhere('CI', 'like', "%{$search}%");
                });
            });
        }

        if (!empty($filtros['gestion'])) {
            $query->where('Gestion', $filtros['gestion']);
        }

        if (!empty($filtros['mes'])) {
            $query->where('Mes', $filtros['mes']);
        }

        if (!empty($filtros['estado_pago'])) {
            $query->where('EstadoPago', $filtros['estado_pago']);
        }

        if (!empty($filtros['empleado_id'])) {
            $query->whereHas('contrato', function ($q) use ($filtros) {
                $q->where('IDEmpleado', $filtros['empleado_id']);
            });
        }

        if (!empty($filtros['departamento_id'])) {
            $query->whereHas('contrato', function ($q) use ($filtros) {
                $q->where('IDDepartamento', $filtros['departamento_id']);
            });
        }

        return $query->orderBy('Gestion', 'desc')
                     ->orderBy('Mes', 'desc')
                     ->paginate($perPage);
    }

    public function obtenerPorId($id): Planilla
    {
        $planilla = Planilla::with(['contrato.empleado'])->find($id);
        
        if (!$planilla) {
            throw new Exception("Planilla no encontrada", 404);
        }

        return $planilla;
    }

    public function generarPlanillaPorPeriodo(int $gestion, int $mes, array $empleados = [], array $opciones = []): array
    {
        DB::beginTransaction();

        try {
            $planillasGeneradas = [];
            
            // Si no se especifican empleados, generar para todos los empleados con contrato activo
            if (empty($empleados)) {
                $contratos = Contrato::where('Estado', 1)->get();
            } else {
                $contratos = Contrato::whereIn('IDEmpleado', $empleados)
                                   ->where('Estado', 1)
                                   ->get();
            }

            foreach ($contratos as $contrato) {
                // Verificar si ya existe planilla para este período
                $existePlanilla = Planilla::where('IDContrato', $contrato->IDContrato)
                                         ->where('Mes', $mes)
                                         ->where('Gestion', $gestion)
                                         ->exists();

                if ($existePlanilla && !($opciones['sobrescribir'] ?? false)) {
                    continue; // Saltar si ya existe y no se permite sobrescribir
                }

                // Eliminar planilla existente si se permite sobrescribir
                if ($existePlanilla && ($opciones['sobrescribir'] ?? false)) {
                    Planilla::where('IDContrato', $contrato->IDContrato)
                           ->where('Mes', $mes)
                           ->where('Gestion', $gestion)
                           ->delete();
                }

                $diasTrabajados = $opciones['dias_trabajados'] ?? 30;
                
                $planilla = Planilla::generarPlanillaPorContrato(
                    $contrato->IDContrato,
                    $mes,
                    $gestion,
                    $diasTrabajados
                );

                $planillasGeneradas[] = $planilla;
            }

            DB::commit();
            
            return $planillasGeneradas;

        } catch (Exception $e) {
            DB::rollback();
            throw new Exception("Error al generar planillas: " . $e->getMessage(), 422);
        }
    }

    public function actualizar($id, array $datos): bool
    {
        $planilla = $this->obtenerPorId($id);

        // Solo permitir actualizar ciertos campos
        $camposPermitidos = [
            'DiasTrabajos', 'SalarioBasico', 'TotalIngresos', 
            'TotalDescuentos', 'LiquidoPagable', 'EstadoPago'
        ];

        $datosLimpios = array_intersect_key($datos, array_flip($camposPermitidos));

        // Si se actualiza el salario o días, recalcular totales
        if (isset($datosLimpios['SalarioBasico']) || isset($datosLimpios['DiasTrabajos'])) {
            $planilla->fill($datosLimpios);
            $calculos = $planilla->calcularTotales();
            $datosLimpios['TotalIngresos'] = $calculos['TotalIngresos'];
            $datosLimpios['TotalDescuentos'] = $calculos['TotalDescuentos'];
            $datosLimpios['LiquidoPagable'] = $calculos['LiquidoPagable'];
        }

        return $planilla->update($datosLimpios);
    }

    public function marcarComoPagada($id, string $fechaPago): bool
    {
        $planilla = $this->obtenerPorId($id);

        if ($planilla->EstadoPago === 'Pagado') {
            throw new Exception("La planilla ya está marcada como pagada", 422);
        }

        if ($planilla->EstadoPago === 'Anulado') {
            throw new Exception("No se puede pagar una planilla anulada", 422);
        }

        return $planilla->update([
            'EstadoPago' => 'Pagado',
            'FechaPago' => $fechaPago
        ]);
    }

    public function anular($id, string $motivo = 'Anulación solicitada'): bool
    {
        $planilla = $this->obtenerPorId($id);

        if ($planilla->EstadoPago === 'Anulado') {
            throw new Exception("La planilla ya está anulada", 422);
        }

        return $planilla->update([
            'EstadoPago' => 'Anulado',
            'FechaPago' => null
        ]);
    }

    public function obtenerPorPeriodo(int $gestion, int $mes)
    {
        return Planilla::with(['contrato.empleado'])
                      ->where('Gestion', $gestion)
                      ->where('Mes', $mes)
                      ->get();
    }

    public function obtenerPorEmpleado(int $empleadoId, int $gestion = null, int $limite = 12)
    {
        $query = Planilla::with(['contrato'])
                         ->whereHas('contrato', function ($q) use ($empleadoId) {
                             $q->where('IDEmpleado', $empleadoId);
                         });

        if ($gestion) {
            $query->where('Gestion', $gestion);
        }

        return $query->orderBy('Gestion', 'desc')
                     ->orderBy('Mes', 'desc')
                     ->limit($limite)
                     ->get();
    }

    public function obtenerEstadisticas(int $gestion, int $mes = null): array
    {
        $query = Planilla::query();

        if ($mes) {
            $query->where('Gestion', $gestion)->where('Mes', $mes);
        } else {
            $query->where('Gestion', $gestion);
        }

        $estadisticas = [
            'total_planillas' => $query->count(),
            'total_pagado' => $query->where('EstadoPago', 'Pagado')->sum('LiquidoPagable'),
            'total_pendiente' => $query->where('EstadoPago', 'Pendiente')->sum('LiquidoPagable'),
            'por_estado' => $query->select('EstadoPago', DB::raw('count(*) as cantidad'), DB::raw('sum(LiquidoPagable) as total'))
                                  ->groupBy('EstadoPago')
                                  ->get(),
        ];

        if (!$mes) {
            // Estadísticas por mes para toda la gestión
            $estadisticas['por_mes'] = Planilla::select('Mes', DB::raw('count(*) as cantidad'), DB::raw('sum(LiquidoPagable) as total'))
                                               ->where('Gestion', $gestion)
                                               ->groupBy('Mes')
                                               ->orderBy('Mes')
                                               ->get();
        }

        return $estadisticas;
    }

    public function generarReporte(int $gestion, int $mes, array $filtros = [], string $formato = 'json'): array
    {
        $query = Planilla::with(['contrato.empleado', 'contrato.departamento', 'contrato.cargo']);

        $query->where('Gestion', $gestion)->where('Mes', $mes);

        // Aplicar filtros adicionales
        if (!empty($filtros['departamento_id'])) {
            $query->whereHas('contrato', function ($q) use ($filtros) {
                $q->where('IDDepartamento', $filtros['departamento_id']);
            });
        }

        if (!empty($filtros['cargo_id'])) {
            $query->whereHas('contrato', function ($q) use ($filtros) {
                $q->where('IDCargo', $filtros['cargo_id']);
            });
        }

        if (!empty($filtros['estado_pago'])) {
            $query->where('EstadoPago', $filtros['estado_pago']);
        }

        $planillas = $query->get();

        // Calcular totales
        $totales = [
            'cantidad_empleados' => $planillas->count(),
            'total_ingresos' => $planillas->sum('TotalIngresos'),
            'total_descuentos' => $planillas->sum('TotalDescuentos'),
            'total_liquido_pagable' => $planillas->sum('LiquidoPagable'),
            'promedio_salario' => $planillas->avg('LiquidoPagable'),
        ];

        return [
            'planillas' => $planillas,
            'totales' => $totales,
            'periodo' => [
                'gestion' => $gestion,
                'mes' => $mes,
                'periodo_texto' => $planillas->first()?->periodo_texto ?? "{$mes}/{$gestion}"
            ]
        ];
    }

    public function generarPlanillaMensual($contratoId, int $mes, int $gestion, int $diasTrabajados = 30): ?Planilla
    {
        try {
            DB::beginTransaction();

            // Verificar si ya existe planilla para este período
            $existePlanilla = Planilla::where('IDContrato', $contratoId)
                                     ->where('Mes', $mes)
                                     ->where('Gestion', $gestion)
                                     ->exists();

            if ($existePlanilla) {
                throw new Exception("Ya existe una planilla para este período");
            }

            // Generar la planilla usando el método estático del modelo
            $planilla = Planilla::generarPlanillaPorContrato($contratoId, $mes, $gestion, $diasTrabajados);

            DB::commit();
            return $planilla;

        } catch (Exception $e) {
            DB::rollback();
            throw new Exception("Error al generar planilla mensual: " . $e->getMessage());
        }
    }
}