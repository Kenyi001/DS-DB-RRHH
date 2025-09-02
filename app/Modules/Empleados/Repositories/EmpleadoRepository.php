<?php

namespace App\Modules\Empleados\Repositories;

use App\Modules\Empleados\Models\Empleado;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmpleadoRepository
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

        if (!empty($filtros['ciudad'])) {
            $query->where('ciudad', 'like', "%{$filtros['ciudad']}%");
        }

        if (!empty($filtros['fecha_ingreso_desde'])) {
            $query->where('fecha_ingreso', '>=', $filtros['fecha_ingreso_desde']);
        }

        if (!empty($filtros['fecha_ingreso_hasta'])) {
            $query->where('fecha_ingreso', '<=', $filtros['fecha_ingreso_hasta']);
        }

        // Ordenamiento
        $orderBy = $filtros['order_by'] ?? 'apellido_paterno';
        $orderDirection = $filtros['order_direction'] ?? 'asc';
        
        if ($orderBy === 'nombre_completo') {
            $query->orderBy('apellido_paterno', $orderDirection)
                  ->orderBy('nombres', $orderDirection);
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        return $query->paginate($perPage);
    }

    public function getActivos(): Collection
    {
        return $this->model->activos()
                          ->orderBy('apellido_paterno')
                          ->orderBy('nombres')
                          ->get();
    }

    public function findById(int $id): ?Empleado
    {
        return $this->model->find($id);
    }

    public function findByCi(string $ci): ?Empleado
    {
        return $this->model->where('ci', $ci)->first();
    }

    public function findByEmail(string $email): ?Empleado
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByCodigoEmpleado(string $codigo): ?Empleado
    {
        return $this->model->where('codigo_empleado', $codigo)->first();
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

        return $empleado->delete();
    }

    public function marcarComoBaja(int $id, string $motivo = null, string $usuario = null): bool
    {
        $empleado = $this->findById($id);
        if (!$empleado) {
            return false;
        }

        $empleado->marcarComoBaja($motivo, $usuario);
        return true;
    }

    public function reactivar(int $id): bool
    {
        $empleado = $this->findById($id);
        if (!$empleado) {
            return false;
        }

        $empleado->reactivar();
        return true;
    }

    public function getEstadisticas(): array
    {
        return [
            'total' => $this->model->count(),
            'activos' => $this->model->where('estado', 'Activo')->count(),
            'inactivos' => $this->model->where('estado', 'Inactivo')->count(),
            'en_vacaciones' => $this->model->where('estado', 'Vacaciones')->count(),
            'con_licencia' => $this->model->where('estado', 'Licencia')->count(),
            'por_genero' => [
                'masculino' => $this->model->where('genero', 'M')->count(),
                'femenino' => $this->model->where('genero', 'F')->count(),
            ],
            'nuevos_este_mes' => $this->model
                ->whereMonth('fecha_ingreso', now()->month)
                ->whereYear('fecha_ingreso', now()->year)
                ->count()
        ];
    }

    public function buscarParaSelect(string $termino = '', int $limite = 10): Collection
    {
        $query = $this->model->activos();

        if ($termino) {
            $query->where(function ($q) use ($termino) {
                $q->porNombre($termino)
                  ->orWhere('ci', 'like', "%{$termino}%")
                  ->orWhere('codigo_empleado', 'like', "%{$termino}%");
            });
        }

        return $query->select('id', 'nombres', 'apellido_paterno', 'apellido_materno', 'ci', 'codigo_empleado')
                    ->orderBy('apellido_paterno')
                    ->orderBy('nombres')
                    ->limit($limite)
                    ->get();
    }

    public function duplicarCi(string $ci, int $exceptoId = null): bool
    {
        $query = $this->model->where('ci', $ci);
        
        if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
        }
        
        return $query->exists();
    }

    public function duplicarEmail(string $email, int $exceptoId = null): bool
    {
        $query = $this->model->where('email', $email);
        
        if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
        }
        
        return $query->exists();
    }

    public function duplicarCodigoEmpleado(string $codigo, int $exceptoId = null): bool
    {
        $query = $this->model->where('codigo_empleado', $codigo);
        
        if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
        }
        
        return $query->exists();
    }
}