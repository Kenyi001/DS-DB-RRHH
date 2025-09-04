<?php

namespace App\Modules\Empleados\Services;

use App\Modules\Empleados\Models\Empleado;
use App\Modules\Empleados\Repositories\EmpleadoRepositoryAprobado as EmpleadoRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EmpleadoService
{
    protected $empleadoRepository;

    public function __construct(EmpleadoRepository $empleadoRepository)
    {
        $this->empleadoRepository = $empleadoRepository;
    }

    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->empleadoRepository->getAll($filtros, $perPage);
        } catch (Exception $e) {
            Log::error('Error listando empleados: ' . $e->getMessage());
            throw new Exception('Error al obtener la lista de empleados.');
        }
    }

    public function obtenerActivos(): Collection
    {
        try {
            return $this->empleadoRepository->getActivos();
        } catch (Exception $e) {
            Log::error('Error obteniendo empleados activos: ' . $e->getMessage());
            throw new Exception('Error al obtener empleados activos.');
        }
    }

    public function obtenerPorId($id): ?Empleado
    {
        try {
            $empleado = $this->empleadoRepository->findById($id);
            
            if (!$empleado) {
                throw new Exception('Empleado no encontrado.');
            }

            return $empleado;
        } catch (Exception $e) {
            Log::error("Error obteniendo empleado ID {$id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function crear(array $data): Empleado
    {
        try {
            DB::beginTransaction();

            // Validaciones de negocio
            $this->validarDatosUnicos($data);
            
            // Generar código de empleado si no se proporciona
            if (empty($data['codigo_empleado'])) {
                $data['codigo_empleado'] = $this->generarCodigoEmpleado($data);
            }

            // Validaciones adicionales
            $this->validarEdadMinima($data['fecha_nacimiento']);
            $this->validarFechaIngreso($data['fecha_ingreso']);

            $empleado = $this->empleadoRepository->create($data);

            DB::commit();

            Log::info("Empleado creado exitosamente", [
                'empleado_id' => $empleado->id,
                'ci' => $empleado->ci,
                'nombre_completo' => $empleado->nombre_completo
            ]);

            return $empleado;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creando empleado: ' . $e->getMessage(), ['data' => $data]);
            throw $e;
        }
    }

    public function actualizar($id, array $data): bool
    {
        try {
            DB::beginTransaction();

            $empleado = $this->obtenerPorId($id);

            // Validaciones de negocio (excluyendo el registro actual)
            $this->validarDatosUnicos($data, $id);
            
            // Validaciones adicionales
            if (isset($data['fecha_nacimiento'])) {
                $this->validarEdadMinima($data['fecha_nacimiento']);
            }

            if (isset($data['fecha_ingreso'])) {
                $this->validarFechaIngreso($data['fecha_ingreso']);
            }

            $actualizado = $this->empleadoRepository->update($id, $data);

            if (!$actualizado) {
                throw new Exception('No se pudo actualizar el empleado.');
            }

            DB::commit();

            Log::info("Empleado actualizado exitosamente", [
                'empleado_id' => $id,
                'datos_actualizados' => array_keys($data)
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error actualizando empleado ID {$id}: " . $e->getMessage(), ['data' => $data]);
            throw $e;
        }
    }

    public function eliminar($id, string $motivo = null, string $usuario = null): bool
    {
        try {
            DB::beginTransaction();

            $empleado = $this->obtenerPorId($id);

            // En lugar de eliminar físicamente, marcamos como baja
            $eliminado = $this->empleadoRepository->marcarComoBaja($id, $motivo, $usuario);

            if (!$eliminado) {
                throw new Exception('No se pudo marcar como baja al empleado.');
            }

            DB::commit();

            Log::info("Empleado marcado como baja", [
                'empleado_id' => $id,
                'motivo' => $motivo,
                'usuario' => $usuario
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error marcando baja empleado ID {$id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function reactivar($id): bool
    {
        try {
            DB::beginTransaction();

            $empleado = $this->obtenerPorId($id);

            $reactivado = $this->empleadoRepository->reactivar($id);

            if (!$reactivado) {
                throw new Exception('No se pudo reactivar el empleado.');
            }

            DB::commit();

            Log::info("Empleado reactivado exitosamente", ['empleado_id' => $id]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error reactivando empleado ID {$id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerEstadisticas(): array
    {
        try {
            return $this->empleadoRepository->getEstadisticas();
        } catch (Exception $e) {
            Log::error('Error obteniendo estadísticas de empleados: ' . $e->getMessage());
            throw new Exception('Error al obtener estadísticas.');
        }
    }

    public function buscarParaSelect(string $termino = '', int $limite = 10): Collection
    {
        try {
            return $this->empleadoRepository->buscarParaSelect($termino, $limite);
        } catch (Exception $e) {
            Log::error('Error en búsqueda para select: ' . $e->getMessage());
            throw new Exception('Error en la búsqueda.');
        }
    }

    // Métodos privados de validación
    private function validarDatosUnicos(array $data, int $exceptoId = null): void
    {
        if (isset($data['ci']) && $this->empleadoRepository->duplicarCi($data['ci'], $exceptoId)) {
            throw new Exception('Ya existe un empleado con el CI: ' . $data['ci']);
        }

        if (isset($data['email']) && $this->empleadoRepository->duplicarEmail($data['email'], $exceptoId)) {
            throw new Exception('Ya existe un empleado with el email: ' . $data['email']);
        }

        if (isset($data['codigo_empleado']) && $this->empleadoRepository->duplicarCodigoEmpleado($data['codigo_empleado'], $exceptoId)) {
            throw new Exception('Ya existe un empleado con el código: ' . $data['codigo_empleado']);
        }
    }

    private function validarEdadMinima(string $fechaNacimiento): void
    {
        $edad = now()->diffInYears($fechaNacimiento);
        
        if ($edad < 18) {
            throw new Exception('El empleado debe ser mayor de 18 años.');
        }

        if ($edad > 80) {
            throw new Exception('La edad máxima permitida es 80 años.');
        }
    }

    private function validarFechaIngreso(string $fechaIngreso): void
    {
        $fecha = \Carbon\Carbon::parse($fechaIngreso);

        if ($fecha->isFuture()) {
            throw new Exception('La fecha de ingreso no puede ser futura.');
        }

        // No permitir fechas muy antiguas (más de 50 años)
        if ($fecha->diffInYears(now()) > 50) {
            throw new Exception('La fecha de ingreso es muy antigua.');
        }
    }

    private function generarCodigoEmpleado(array $data): string
    {
        // Generar código: EMP + año ingreso + secuencial
        $año = \Carbon\Carbon::parse($data['fecha_ingreso'])->format('y');
        $iniciales = strtoupper(substr($data['nombres'], 0, 1) . substr($data['apellido_paterno'], 0, 1));
        
        // Buscar el próximo número secuencial
        $ultimoEmpleado = Empleado::where('codigo_empleado', 'like', "EMP{$año}%")
                                   ->orderBy('codigo_empleado', 'desc')
                                   ->first();

        if ($ultimoEmpleado) {
            $ultimoNumero = (int) substr($ultimoEmpleado->codigo_empleado, -3);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return "EMP{$año}" . str_pad($nuevoNumero, 3, '0', STR_PAD_LEFT);
    }

    public function validarCiBoliviano(string $ci): bool
    {
        // Validación básica de CI boliviano
        if (strlen($ci) < 6 || strlen($ci) > 10) {
            return false;
        }

        // Verificar que sean solo números (excepto el último dígito que puede ser verificador)
        if (!preg_match('/^\d{6,9}[0-9A-Z]?$/', $ci)) {
            return false;
        }

        return true;
    }

    public function generarReporteEmpleados(array $filtros = []): array
    {
        try {
            $empleados = $this->empleadoRepository->getAll($filtros, 1000);
            $estadisticas = $this->obtenerEstadisticas();

            return [
                'empleados' => $empleados->items(),
                'estadisticas' => $estadisticas,
                'total_registros' => $empleados->total(),
                'fecha_generacion' => now()->format('Y-m-d H:i:s'),
                'filtros_aplicados' => $filtros
            ];

        } catch (Exception $e) {
            Log::error('Error generando reporte de empleados: ' . $e->getMessage());
            throw new Exception('Error al generar el reporte.');
        }
    }
}