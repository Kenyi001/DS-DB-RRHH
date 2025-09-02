<?php

namespace App\Modules\Planillas\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PlanillaCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'planillas' => PlanillaResource::collection($this->collection),
            'meta' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'estadisticas' => $this->when($request->input('incluir_estadisticas'), function () {
                return [
                    'total_liquido_pagable' => $this->collection->sum('LiquidoPagable'),
                    'total_ingresos' => $this->collection->sum('TotalIngresos'),
                    'total_descuentos' => $this->collection->sum('TotalDescuentos'),
                    'promedio_salario' => $this->collection->avg('LiquidoPagable'),
                    'por_estado' => $this->collection->groupBy('EstadoPago')->map(function ($group, $estado) {
                        return [
                            'cantidad' => $group->count(),
                            'total' => $group->sum('LiquidoPagable')
                        ];
                    }),
                ];
            }),
        ];
    }
}