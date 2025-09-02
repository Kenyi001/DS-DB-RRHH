<?php

namespace App\Modules\Contratos\Repositories;

use App\Models\Contrato;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ContratoRepository
{
    protected $model;

    public function __construct(Contrato $contrato)
    {
        $this->model = $contrato;
    }

    public function obtenerTodos(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['empleado', 'cargo', 'departamento', 'categoria']);

        // Filtros
        if (!empty($filtros['buscar'])) {
            $query->whereHas('empleado', function($q) use ($filtros) {
                $q->where('Nombres', 'like', '%' . $filtros['buscar'] . '%')
                  ->orWhere('ApellidoPaterno', 'like', '%' . $filtros['buscar'] . '%')
                  ->orWhere('ApellidoMaterno', 'like', '%' . $filtros['buscar'] . '%');
            })->orWhere('NumeroContrato', 'like', '%' . $filtros['buscar'] . '%');
        }

        if (!empty($filtros['estado'])) {
            $query->where('Estado', $filtros['estado'] === 'activo' ? 1 : 0);
        }

        if (!empty($filtros['tipo_contrato'])) {
            $query->where('TipoContrato', $filtros['tipo_contrato']);
        }

        if (!empty($filtros['departamento_id'])) {
            $query->where('IDDepartamento', $filtros['departamento_id']);
        }

        if (!empty($filtros['solo_vigentes'])) {
            $query->vigentes();
        }

        if (!empty($filtros['fecha_inicio_desde'])) {
            $query->where('FechaInicio', '>=', $filtros['fecha_inicio_desde']);
        }

        if (!empty($filtros['fecha_inicio_hasta'])) {
            $query->where('FechaInicio', '<=', $filtros['fecha_inicio_hasta']);
        }

        // Ordenamiento
        $orderBy = $filtros['order_by'] ?? 'FechaInicio';
        $orderDirection = $filtros['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        return $query->paginate($perPage);
    }

    public function obtenerPorId(int $id): ?Contrato
    {
        return $this->model->with(['empleado', 'cargo', 'departamento', 'categoria'])->find($id);
    }

    public function crear(array $datos): Contrato
    {
        return $this->model->create($datos);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $contrato = $this->model->find($id);
        if (!$contrato) {
            return false;
        }
        
        return $contrato->update($datos);
    }

    public function eliminar(int $id): bool
    {
        $contrato = $this->model->find($id);
        if (!$contrato) {
            return false;
        }

        return $contrato->update(['Estado' => 0]);
    }

    public function reactivar(int $id): bool
    {
        $contrato = $this->model->find($id);
        if (!$contrato) {
            return false;
        }

        return $contrato->update(['Estado' => 1]);
    }

    public function obtenerActivos(): Collection
    {
        return $this->model->with(['empleado', 'cargo', 'departamento'])
                          ->activos()
                          ->get();
    }

    public function obtenerVigentes(): Collection
    {
        return $this->model->with(['empleado', 'cargo', 'departamento'])
                          ->activos()
                          ->vigentes()
                          ->get();
    }

    public function obtenerPorEmpleado(int $empleadoId): Collection
    {
        return $this->model->with(['cargo', 'departamento', 'categoria'])
                          ->where('IDEmpleado', $empleadoId)
                          ->orderBy('FechaInicio', 'desc')
                          ->get();
    }

    public function obtenerContratoVigenteEmpleado(int $empleadoId): ?Contrato
    {
        return $this->model->with(['cargo', 'departamento', 'categoria'])
                          ->where('IDEmpleado', $empleadoId)
                          ->activos()
                          ->vigentes()
                          ->first();
    }

    public function obtenerEstadisticas(): array
    {
        $total = $this->model->count();
        $activos = $this->model->activos()->count();
        $vigentes = $this->model->activos()->vigentes()->count();
        $porVencer = $this->model->activos()
                                ->whereNotNull('FechaFin')
                                ->whereBetween('FechaFin', [now(), now()->addDays(30)])
                                ->count();

        $porTipo = $this->model->activos()
                              ->selectRaw('TipoContrato, COUNT(*) as total')
                              ->groupBy('TipoContrato')
                              ->pluck('total', 'TipoContrato')
                              ->toArray();

        return [
            'total' => $total,
            'activos' => $activos,
            'vigentes' => $vigentes,
            'por_vencer_30_dias' => $porVencer,
            'por_tipo' => $porTipo,
            'inactivos' => $total - $activos
        ];
    }

    public function buscarParaSelect(string $termino = '', int $limite = 10): Collection
    {
        $query = $this->model->with('empleado')
                            ->activos()
                            ->vigentes();

        if ($termino) {
            $query->where(function($q) use ($termino) {
                $q->where('NumeroContrato', 'like', '%' . $termino . '%')
                  ->orWhereHas('empleado', function($subQ) use ($termino) {
                      $subQ->where('Nombres', 'like', '%' . $termino . '%')
                           ->orWhere('ApellidoPaterno', 'like', '%' . $termino . '%');
                  });
            });
        }

        return $query->limit($limite)->get();
    }
}