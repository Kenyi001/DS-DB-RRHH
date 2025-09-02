<?php

namespace App\Modules\Contratos\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContratoCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'contratos' => ContratoResource::collection($this->collection),
            'resumen' => [
                'total_contratos' => $this->collection->count(),
                'vigentes' => $this->collection->filter(function($contrato) {
                    return $contrato->es_vigente && $contrato->Estado;
                })->count(),
                'por_vencer_30_dias' => $this->collection->filter(function($contrato) {
                    return $contrato->dias_vigencia !== null && 
                           $contrato->dias_vigencia <= 30 && 
                           $contrato->dias_vigencia > 0;
                })->count(),
                'vencidos' => $this->collection->filter(function($contrato) {
                    return $contrato->dias_vigencia !== null && $contrato->dias_vigencia < 0;
                })->count(),
                'inactivos' => $this->collection->filter(function($contrato) {
                    return !$contrato->Estado;
                })->count(),
                'por_tipo' => $this->agruparPorTipo(),
                'haber_basico_promedio' => $this->calcularHaberPromedio(),
                'duracion_promedio_meses' => $this->calcularDuracionPromedio()
            ]
        ];
    }
    
    private function agruparPorTipo()
    {
        return $this->collection
                   ->where('Estado', true)
                   ->groupBy('TipoContrato')
                   ->map(function($contratos, $tipo) {
                       return [
                           'tipo' => $tipo,
                           'cantidad' => $contratos->count(),
                           'haber_promedio' => $contratos->avg('HaberBasico')
                       ];
                   })
                   ->values()
                   ->toArray();
    }
    
    private function calcularHaberPromedio()
    {
        $activos = $this->collection->where('Estado', true);
        
        if ($activos->isEmpty()) {
            return 0;
        }
        
        return round($activos->avg('HaberBasico'), 2);
    }
    
    private function calcularDuracionPromedio()
    {
        $contratosConFin = $this->collection
                               ->where('Estado', true)
                               ->whereNotNull('FechaFin');
        
        if ($contratosConFin->isEmpty()) {
            return 'N/A (mayoría indefinidos)';
        }
        
        $duracionPromedio = $contratosConFin->map(function($contrato) {
            return $contrato->FechaInicio->diffInMonths($contrato->FechaFin);
        })->avg();
        
        return round($duracionPromedio, 1) . ' meses';
    }
}