<?php

namespace App\Modules\Empleados\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpleadoResourceAprobado extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'IDEmpleado' => $this->IDEmpleado,
            
            // Información personal (usando campos de la estructura aprobada)
            'Nombres' => $this->Nombres,
            'ApellidoPaterno' => $this->ApellidoPaterno,
            'ApellidoMaterno' => $this->ApellidoMaterno,
            'nombre_completo' => $this->nombre_completo, // Accessor
            'FechaNacimiento' => $this->FechaNacimiento?->format('Y-m-d'),
            'edad' => $this->edad, // Accessor
            
            // Información de contacto
            'Telefono' => $this->Telefono,
            'Email' => $this->Email,
            'Direccion' => $this->Direccion,
            
            // Información laboral
            'FechaIngreso' => $this->FechaIngreso?->format('Y-m-d'),
            'antiguedad_anos' => $this->antiguedad_anos, // Accessor
            'Estado' => $this->Estado,
            'estado_texto' => $this->Estado ? 'Activo' : 'Inactivo',
            'esta_activo' => (bool) $this->Estado,
        ];
    }
}