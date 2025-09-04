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

    public function findById($id): ?Empleado
    {
        return $this->model->find($id);
    }

    public function create(array $data): Empleado
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $empleado = $this->findById($id);
        if (!$empleado) {
            return false;
        }

        return $empleado->update($data);
    }

    public function delete($id): bool
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

    public function marcarComoBaja($id, string $motivo = null, string $usuario = null): bool
    {
        $empleado = $this->findById($id);
        if (!$empleado) {
            return false;
        }

        return $empleado->update([
            'Estado' => 0,
            'FechaBaja' => now(),
            'MotivoBaja' => $motivo,
            'UsuarioBaja' => $usuario
        ]);
    }

    public function reactivar($id): bool
    {
        $empleado = $this->findById($id);
        if (!$empleado) {
            return false;
        }

        return $empleado->update([
            'Estado' => 1,
            'FechaBaja' => null,
            'MotivoBaja' => null,
            'UsuarioBaja' => null
        ]);
    }

    public function duplicarCi(string $ci, $exceptoId = null): bool
    {
        $query = $this->model->where('CI', $ci);
        if ($exceptoId) {
            $query->where('IDEmpleado', '!=', $exceptoId);
        }
        return $query->exists();
    }

    public function duplicarEmail(string $email, $exceptoId = null): bool
    {
        $query = $this->model->where('Email', $email);
        if ($exceptoId) {
            $query->where('IDEmpleado', '!=', $exceptoId);
        }
        return $query->exists();
    }

    public function duplicarCodigoEmpleado(string $codigo, $exceptoId = null): bool
    {
        $query = $this->model->where('CodigoEmpleado', $codigo);
        if ($exceptoId) {
            $query->where('IDEmpleado', '!=', $exceptoId);
        }
        return $query->exists();
    }

    public function getEstadisticas(): array
    {
        return [
            'total' => $this->model->count(),
            'activos' => $this->model->activos()->count(),
            'inactivos' => $this->model->where('Estado', 0)->count(),
            'nuevos_mes' => $this->model->activos()
                                      ->whereMonth('FechaIngreso', now()->month)
                                      ->whereYear('FechaIngreso', now()->year)
                                      ->count()
        ];
    }

    public function buscarParaSelect(string $termino = '', int $limite = 10): Collection
    {
        $query = $this->model->activos();

        if ($termino) {
            $query->porNombre($termino);
        }

        return $query->limit($limite)->get();
    }
}