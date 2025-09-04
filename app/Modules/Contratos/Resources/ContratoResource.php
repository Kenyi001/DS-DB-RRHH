<?php

namespace App\Modules\Contratos\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContratoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'IDContrato' => $this->IDContrato,
            'IDEmpleado' => $this->IDEmpleado,
            'IDCategoria' => $this->IDCategoria,
            'IDCargo' => $this->IDCargo,
            'IDDepartamento' => $this->IDDepartamento,
            'NumeroContrato' => $this->NumeroContrato,
            'TipoContrato' => $this->TipoContrato,
            'FechaContrato' => $this->FechaContrato?->format('Y-m-d'),
            'FechaInicio' => $this->FechaInicio?->format('Y-m-d'),
            'FechaFin' => $this->FechaFin?->format('Y-m-d'),
            'HaberBasico' => $this->HaberBasico,
            'Estado' => $this->Estado,
            
            // Campos calculados
            'estado_texto' => $this->estado_texto,
            'es_vigente' => $this->es_vigente,
            'dias_vigencia' => $this->dias_vigencia,
            
            // Relaciones cuando están cargadas
            'empleado' => $this->when($this->relationLoaded('empleado'), function () {
                return [
                    'IDEmpleado' => $this->empleado->IDEmpleado,
                    'Nombres' => $this->empleado->Nombres,
                    'ApellidoPaterno' => $this->empleado->ApellidoPaterno,
                    'ApellidoMaterno' => $this->empleado->ApellidoMaterno,
                    'nombre_completo' => $this->empleado->nombre_completo,
                    'Email' => $this->empleado->Email,
                    'Telefono' => $this->empleado->Telefono,
                    'Estado' => $this->empleado->Estado
                ];
            }),
            
            'cargo' => $this->when($this->relationLoaded('cargo'), function () {
                return [
                    'IDCargo' => $this->cargo->IDCargo,
                    'NombreCargo' => $this->cargo->NombreCargo,
                    'Descripcion' => $this->cargo->Descripcion,
                    'Estado' => $this->cargo->Estado
                ];
            }),
            
            'departamento' => $this->when($this->relationLoaded('departamento'), function () {
                return [
                    'IDDepartamento' => $this->departamento->IDDepartamento,
                    'NombreDepartamento' => $this->departamento->NombreDepartamento,
                    'Descripcion' => $this->departamento->Descripcion,
                    'Estado' => $this->departamento->Estado
                ];
            }),
            
            'categoria' => $this->when($this->relationLoaded('categoria'), function () {
                return [
                    'IDCategoria' => $this->categoria->IDCategoria,
                    'NombreCategoria' => $this->categoria->NombreCategoria,
                    'HaberBasico' => $this->categoria->HaberBasico,
                    'Estado' => $this->categoria->Estado
                ];
            }),
            
            // Información adicional para el frontend
            'informacion_adicional' => [
                'duracion_meses' => $this->calcularDuracionMeses(),
                'tiempo_servicio' => $this->calcularTiempoServicio(),
                'proxima_renovacion' => $this->calcularProximaRenovacion(),
                'alertas' => $this->generarAlertas()
            ]
        ];
    }
    
    private function calcularDuracionMeses()
    {
        if (!$this->FechaFin) {
            return 'Indefinido';
        }
        
        return $this->FechaInicio->diffInMonths($this->FechaFin);
    }
    
    private function calcularTiempoServicio()
    {
        $fechaReferencia = $this->FechaFin && $this->FechaFin < now() ? $this->FechaFin : now();
        $meses = $this->FechaInicio->diffInMonths($fechaReferencia);
        
        if ($meses < 12) {
            return "{$meses} meses";
        }
        
        $años = intval($meses / 12);
        $mesesRestantes = $meses % 12;
        
        if ($mesesRestantes > 0) {
            return "{$años} años, {$mesesRestantes} meses";
        }
        
        return "{$años} años";
    }
    
    private function calcularProximaRenovacion()
    {
        if (!$this->FechaFin || $this->FechaFin < now()) {
            return null;
        }
        
        // Sugerir renovación 3 meses antes del vencimiento
        $fechaRenovacion = $this->FechaFin->subMonths(3);
        
        return [
            'fecha_sugerida' => $fechaRenovacion->format('Y-m-d'),
            'dias_para_renovacion' => now()->diffInDays($fechaRenovacion, false),
            'requiere_atencion' => $fechaRenovacion <= now()
        ];
    }
    
    private function generarAlertas()
    {
        $alertas = [];
        
        // Alerta de vencimiento próximo
        if ($this->FechaFin && $this->dias_vigencia !== null && $this->dias_vigencia <= 30 && $this->dias_vigencia > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'mensaje' => "El contrato vence en {$this->dias_vigencia} días",
                'accion_sugerida' => 'Considerar renovación'
            ];
        }
        
        // Alerta de contrato vencido
        if ($this->FechaFin && $this->dias_vigencia !== null && $this->dias_vigencia < 0) {
            $alertas[] = [
                'tipo' => 'danger',
                'mensaje' => 'El contrato está vencido',
                'accion_sugerida' => 'Renovar o finalizar formalmente'
            ];
        }
        
        // Alerta de contrato inactivo
        if (!$this->Estado) {
            $alertas[] = [
                'tipo' => 'info',
                'mensaje' => 'Contrato inactivo',
                'accion_sugerida' => 'Verificar estado del empleado'
            ];
        }
        
        return $alertas;
    }
}