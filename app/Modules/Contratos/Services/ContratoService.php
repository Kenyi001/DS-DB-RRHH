<?php

namespace App\Modules\Contratos\Services;

use App\Modules\Contratos\Repositories\ContratoRepository;
use App\Models\Contrato;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class ContratoService
{
    protected $contratoRepository;

    public function __construct(ContratoRepository $contratoRepository)
    {
        $this->contratoRepository = $contratoRepository;
    }

    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->contratoRepository->obtenerTodos($filtros, $perPage);
    }

    public function obtenerPorId(int $id): ?Contrato
    {
        $contrato = $this->contratoRepository->obtenerPorId($id);
        
        if (!$contrato) {
            throw new Exception("Contrato con ID {$id} no encontrado", 404);
        }

        return $contrato;
    }

    public function crear(array $datos): Contrato
    {
        // Validar si ya existe contrato vigente para el empleado
        $contratoVigente = $this->contratoRepository->obtenerContratoVigenteEmpleado($datos['IDEmpleado']);
        
        if ($contratoVigente && empty($datos['permitir_multiple'])) {
            throw new Exception("El empleado ya tiene un contrato vigente. Debe finalizar el contrato actual antes de crear uno nuevo.", 422);
        }

        // Generar número de contrato si no se proporciona
        if (empty($datos['NumeroContrato'])) {
            $datos['NumeroContrato'] = $this->generarNumeroContrato();
        }

        // Validar fechas
        $this->validarFechas($datos);

        // Establecer valores por defecto
        $datos = $this->establecerDefaults($datos);

        return $this->contratoRepository->crear($datos);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $contrato = $this->obtenerPorId($id);

        // Validar fechas si se están actualizando
        if (isset($datos['FechaInicio']) || isset($datos['FechaFin'])) {
            $datosCompletos = array_merge($contrato->toArray(), $datos);
            $this->validarFechas($datosCompletos);
        }

        return $this->contratoRepository->actualizar($id, $datos);
    }

    public function eliminar(int $id, string $motivo = 'Baja de contrato', string $usuario = 'Sistema'): bool
    {
        $contrato = $this->obtenerPorId($id);

        if (!$contrato->Estado) {
            throw new Exception("El contrato ya está inactivo", 422);
        }

        return $this->contratoRepository->eliminar($id);
    }

    public function reactivar(int $id): bool
    {
        $contrato = $this->obtenerPorId($id);

        if ($contrato->Estado) {
            throw new Exception("El contrato ya está activo", 422);
        }

        return $this->contratoRepository->reactivar($id);
    }

    public function finalizarContrato(int $id, string $fechaFin, string $motivo = ''): bool
    {
        $contrato = $this->obtenerPorId($id);

        if ($contrato->FechaFin && $contrato->FechaFin <= now()) {
            throw new Exception("El contrato ya está finalizado", 422);
        }

        $datos = [
            'FechaFin' => $fechaFin
        ];

        return $this->contratoRepository->actualizar($id, $datos);
    }

    public function renovarContrato(int $id, array $datosRenovacion): Contrato
    {
        $contratoOriginal = $this->obtenerPorId($id);

        // Finalizar contrato actual
        $this->finalizarContrato($id, $datosRenovacion['fecha_fin_actual'] ?? now()->toDateString());

        // Crear nuevo contrato basado en el anterior
        $datosNuevo = [
            'IDEmpleado' => $contratoOriginal->IDEmpleado,
            'IDCategoria' => $datosRenovacion['IDCategoria'] ?? $contratoOriginal->IDCategoria,
            'IDCargo' => $datosRenovacion['IDCargo'] ?? $contratoOriginal->IDCargo,
            'IDDepartamento' => $datosRenovacion['IDDepartamento'] ?? $contratoOriginal->IDDepartamento,
            'TipoContrato' => $datosRenovacion['TipoContrato'] ?? $contratoOriginal->TipoContrato,
            'FechaContrato' => $datosRenovacion['FechaContrato'] ?? now()->toDateString(),
            'FechaInicio' => $datosRenovacion['FechaInicio'],
            'FechaFin' => $datosRenovacion['FechaFin'] ?? null,
            'HaberBasico' => $datosRenovacion['HaberBasico'] ?? $contratoOriginal->HaberBasico,
            'permitir_multiple' => true
        ];

        return $this->crear($datosNuevo);
    }

    public function obtenerActivos(): Collection
    {
        return $this->contratoRepository->obtenerActivos();
    }

    public function obtenerVigentes(): Collection
    {
        return $this->contratoRepository->obtenerVigentes();
    }

    public function obtenerPorEmpleado(int $empleadoId): Collection
    {
        return $this->contratoRepository->obtenerPorEmpleado($empleadoId);
    }

    public function obtenerContratoVigenteEmpleado(int $empleadoId): ?Contrato
    {
        return $this->contratoRepository->obtenerContratoVigenteEmpleado($empleadoId);
    }

    public function obtenerEstadisticas(): array
    {
        return $this->contratoRepository->obtenerEstadisticas();
    }

    public function buscarParaSelect(string $termino = '', int $limite = 10): Collection
    {
        return $this->contratoRepository->buscarParaSelect($termino, $limite);
    }

    // Métodos privados de validación y utilidades

    private function validarFechas(array $datos): void
    {
        if (isset($datos['FechaInicio']) && isset($datos['FechaFin'])) {
            if ($datos['FechaInicio'] > $datos['FechaFin']) {
                throw new Exception("La fecha de inicio no puede ser mayor a la fecha de fin", 422);
            }
        }

        if (isset($datos['FechaContrato']) && isset($datos['FechaInicio'])) {
            if ($datos['FechaContrato'] > $datos['FechaInicio']) {
                throw new Exception("La fecha del contrato no puede ser mayor a la fecha de inicio", 422);
            }
        }
    }

    private function establecerDefaults(array $datos): array
    {
        $datos['TipoContrato'] = $datos['TipoContrato'] ?? 'Indefinido';
        $datos['Estado'] = $datos['Estado'] ?? 1;
        $datos['FechaContrato'] = $datos['FechaContrato'] ?? now()->toDateString();

        return $datos;
    }

    private function generarNumeroContrato(): string
    {
        $año = now()->year;
        $ultimoNumero = Contrato::where('NumeroContrato', 'like', "CONT-{$año}-%")
                               ->orderBy('NumeroContrato', 'desc')
                               ->first();

        if ($ultimoNumero) {
            $numero = (int) substr($ultimoNumero->NumeroContrato, -4) + 1;
        } else {
            $numero = 1;
        }

        return sprintf("CONT-%d-%04d", $año, $numero);
    }
}