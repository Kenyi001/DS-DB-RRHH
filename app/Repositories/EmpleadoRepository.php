<?php

namespace App\Repositories;

use App\Models\Empleado;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmpleadoRepository
{
    public function findAll(): Collection
    {
        return Empleado::activos()->with(['contratos'])->get();
    }

    public function findWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Empleado::activos();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('Nombres', 'like', "%{$search}%")
                  ->orWhere('ApellidoPaterno', 'like', "%{$search}%")
                  ->orWhere('CI', 'like', "%{$search}%")
                  ->orWhere('Email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['departamento_id'])) {
            $query->whereHas('contratos', function($q) use ($filters) {
                $q->where('IDDepartamento', $filters['departamento_id'])
                  ->where('Estado', 'Activo');
            });
        }

        return $query->with(['contratos.departamento', 'contratos.cargo'])
                    ->orderBy('ApellidoPaterno')
                    ->orderBy('Nombres')
                    ->paginate($perPage);
    }

    public function findById(int $id): ?Empleado
    {
        return Empleado::with(['contratos.departamento', 'contratos.cargo'])
                      ->find($id);
    }

    public function create(array $data): Empleado
    {
        return Empleado::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Empleado::where('IDEmpleado', $id)->update($data);
    }

    public function softDelete(int $id, string $usuario): bool
    {
        return Empleado::where('IDEmpleado', $id)->update([
            'Estado' => false,
            'FechaBaja' => now(),
            'UsuarioBaja' => $usuario
        ]);
    }

    public function getByDepartamento(int $departamentoId): Collection
    {
        return Empleado::whereHas('contratos', function($q) use ($departamentoId) {
            $q->where('IDDepartamento', $departamentoId)
              ->where('Estado', 'Activo');
        })->activos()->get();
    }
}