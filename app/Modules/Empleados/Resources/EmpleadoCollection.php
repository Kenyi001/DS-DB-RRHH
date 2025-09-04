<?php

namespace App\Modules\Empleados\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmpleadoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'empleados' => EmpleadoResource::collection($this->collection),
            'resumen' => [
                'total_registros' => $this->collection->count(),
                'por_estado' => $this->collection->groupBy('estado')->map->count(),
                'por_genero' => $this->collection->groupBy('genero')->map->count(),
            ]
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'generated_at' => now()->toISOString(),
                'resource_type' => 'empleados_collection'
            ],
        ];
    }
}