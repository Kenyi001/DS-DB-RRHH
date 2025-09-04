<?php

namespace App\Modules\Empleados\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpleadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            // Información personal
            'ci' => $this->ci,
            'nombres' => $this->nombres,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'nombre_completo' => $this->nombre_completo,
            'fecha_nacimiento' => $this->fecha_nacimiento?->format('Y-m-d'),
            'edad' => $this->edad,
            'genero' => $this->genero,
            'genero_texto' => $this->genero === 'M' ? 'Masculino' : 'Femenino',
            'estado_civil' => $this->estado_civil,
            'nacionalidad' => $this->nacionalidad,
            
            // Información de contacto
            'telefono' => $this->telefono,
            'celular' => $this->celular,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            
            // Información laboral
            'codigo_empleado' => $this->codigo_empleado,
            'fecha_ingreso' => $this->fecha_ingreso?->format('Y-m-d'),
            'antiguedad_anos' => $this->antiguedad_anos,
            'estado' => $this->estado,
            
            // Auditoría y control
            'fecha_baja' => $this->fecha_baja?->format('Y-m-d H:i:s'),
            'usuario_baja' => $this->usuario_baja,
            'motivo_baja' => $this->motivo_baja,
            
            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Campos calculados adicionales
            'esta_activo' => $this->estado === 'Activo',
            'tiempo_servicio' => $this->fecha_ingreso ? 
                $this->fecha_ingreso->diffForHumans(null, true) : null,
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
            ],
        ];
    }
}