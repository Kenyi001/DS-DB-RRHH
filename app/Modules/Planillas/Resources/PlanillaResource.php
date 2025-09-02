<?php

namespace App\Modules\Planillas\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanillaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->IDGestionSalario,
            'contrato_id' => $this->IDContrato,
            'mes' => $this->Mes,
            'gestion' => $this->Gestion,
            'periodo_texto' => $this->periodo_texto,
            'dias_trabajos' => $this->DiasTrabajos,
            'salario_basico' => $this->SalarioBasico,
            'total_ingresos' => $this->TotalIngresos,
            'total_descuentos' => $this->TotalDescuentos,
            'liquido_pagable' => $this->LiquidoPagable,
            'fecha_pago' => $this->FechaPago?->format('Y-m-d'),
            'estado_pago' => $this->EstadoPago,
            'estado_texto' => $this->estado_texto,
            'porcentaje_descuentos' => $this->porcentaje_descuentos,
            'salario_diario' => $this->salario_diario,
            'salario_por_dias' => $this->salario_por_dias,
            
            // Información del empleado
            'empleado' => $this->whenLoaded('contrato.empleado', function () {
                return [
                    'id' => $this->contrato->empleado->IDEmpleado,
                    'nombres' => $this->contrato->empleado->Nombres,
                    'apellido_paterno' => $this->contrato->empleado->ApellidoPaterno,
                    'apellido_materno' => $this->contrato->empleado->ApellidoMaterno,
                    'nombre_completo' => $this->contrato->empleado->nombre_completo,
                    'email' => $this->contrato->empleado->Email,
                    'telefono' => $this->contrato->empleado->Telefono,
                ];
            }),
            
            // Información del contrato
            'contrato' => $this->whenLoaded('contrato', function () {
                return [
                    'numero_contrato' => $this->contrato->NumeroContrato,
                    'tipo_contrato' => $this->contrato->TipoContrato,
                    'fecha_inicio' => $this->contrato->FechaInicio,
                    'fecha_fin' => $this->contrato->FechaFin,
                    'haber_basico' => $this->contrato->HaberBasico,
                    'estado' => $this->contrato->Estado ? 'Activo' : 'Inactivo',
                ];
            }),
            
            // Calculos adicionales
            'calculos' => $this->when($request->input('incluir_calculos'), function () {
                return $this->calcularTotales();
            }),
            
            'timestamps' => [
                'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            ],
        ];
    }
}