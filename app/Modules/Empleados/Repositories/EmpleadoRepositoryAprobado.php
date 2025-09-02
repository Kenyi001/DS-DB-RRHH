<?php

namespace App\Modules\Empleados\Repositories;

use App\Modules\Empleados\Models\Empleado;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmpleadoRepositoryAprobado
{
    protected $model;

    public function __construct(Empleado $empleado)
    {
        $this->model = $empleado;
    }

    public function getAll(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Aplicar filtros
        if (!empty($filtros['buscar'])) {
            $termino = $filtros['buscar'];
            $query->where(function ($q) use ($termino) {
                $q->porNombre($termino)
                  ->orWhere('Email', 'like', "%{$termino}%");
            });
        }

        if (!empty($filtros['estado'])) {
            $query->where('Estado', $filtros['estado'] === 'Activo' ? 1 : 0);
        }

        // Ordenamiento
        $query->orderBy('ApellidoPaterno', 'asc')
              ->orderBy('Nombres', 'asc');

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Empleado
    {
        return $this->model->find($id);
    }

    public function create(array $data): Empleado
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $empleado = $this->findById($id);
        if (!$empleado) {
            return false;
        }

        return $empleado->update($data);
    }

    public function delete(int $id): bool
    {
        $empleado = $this->findById($id);
        if (!$empleado) {
            return false;
        }

        // En la estructura aprobada, "eliminar" es cambiar Estado a 0
        return $empleado->update(['Estado' => 0]);
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function getActivos(): Collection
    {
        return $this->model->activos()->get();
    }
}