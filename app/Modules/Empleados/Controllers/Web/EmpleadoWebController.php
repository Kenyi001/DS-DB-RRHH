<?php

namespace App\Modules\Empleados\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Empleados\Services\EmpleadoService;
use App\Modules\Empleados\Requests\StoreEmpleadoRequest;
use App\Modules\Empleados\Requests\UpdateEmpleadoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpleadoWebController extends Controller
{
    protected $empleadoService;

    public function __construct(EmpleadoService $empleadoService)
    {
        $this->empleadoService = $empleadoService;
    }

    public function index(Request $request)
    {
        $filtros = $request->only(['search', 'estado', 'departamento_id']);
        $perPage = $request->get('per_page', 20);
        
        try {
            // Obtener empleados paginados
            $empleados = $this->empleadoService->listar($filtros, $perPage);
            
            // Obtener departamentos para el filtro
            $departamentos = DB::table('Departamentos')
                               ->where('Estado', 1)
                               ->orderBy('NombreDepartamento')
                               ->get();
            
            return view('empleados.index', compact('empleados', 'departamentos', 'filtros'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar empleados: ' . $e->getMessage());
        }
    }

    public function create()
    {
        // Obtener datos maestros para el formulario
        $departamentos = DB::table('Departamentos')
                           ->where('Estado', 1)
                           ->orderBy('NombreDepartamento')
                           ->get();
        
        return view('empleados.create', compact('departamentos'));
    }

    public function store(StoreEmpleadoRequest $request)
    {
        try {
            $empleado = $this->empleadoService->crear($request->validated());
            
            return redirect()
                ->route('empleados.show', $empleado->IDEmpleado)
                ->with('success', 'Empleado creado exitosamente.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear empleado: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $empleado = $this->empleadoService->obtenerPorId($id);
            
            // Obtener contratos del empleado
            $contratos = DB::table('Contratos')
                          ->join('Cargos', 'Contratos.IDCargo', '=', 'Cargos.IDCargo')
                          ->join('Departamentos', 'Contratos.IDDepartamento', '=', 'Departamentos.IDDepartamento')
                          ->join('Categorias', 'Contratos.IDCategoria', '=', 'Categorias.IDCategoria')
                          ->where('Contratos.IDEmpleado', $id)
                          ->select(
                              'Contratos.*',
                              'Cargos.NombreCargo',
                              'Departamentos.NombreDepartamento',
                              'Categorias.NombreCategoria'
                          )
                          ->orderBy('Contratos.FechaInicio', 'desc')
                          ->get();
            
            // Obtener planillas recientes
            $planillas = DB::table('GestionSalarios')
                          ->join('Contratos', 'GestionSalarios.IDContrato', '=', 'Contratos.IDContrato')
                          ->where('Contratos.IDEmpleado', $id)
                          ->select('GestionSalarios.*')
                          ->orderBy('Gestion', 'desc')
                          ->orderBy('Mes', 'desc')
                          ->limit(12)
                          ->get();
            
            return view('empleados.show', compact('empleado', 'contratos', 'planillas'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar empleado: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $empleado = $this->empleadoService->obtenerPorId($id);
            
            // Obtener departamentos para el formulario
            $departamentos = DB::table('Departamentos')
                               ->where('Estado', 1)
                               ->orderBy('NombreDepartamento')
                               ->get();
            
            return view('empleados.edit', compact('empleado', 'departamentos'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar empleado: ' . $e->getMessage());
        }
    }

    public function update(UpdateEmpleadoRequest $request, $id)
    {
        try {
            $actualizado = $this->empleadoService->actualizar($id, $request->validated());
            
            if ($actualizado) {
                return redirect()
                    ->route('empleados.show', $id)
                    ->with('success', 'Empleado actualizado exitosamente.');
            }
            
            return back()
                ->withInput()
                ->with('error', 'No se pudo actualizar el empleado.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar empleado: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // Solo desactivar, no eliminar físicamente
            $actualizado = $this->empleadoService->actualizar($id, [
                'estado' => 0,
                'fecha_baja' => now(),
                'motivo_baja' => 'Eliminado desde sistema web',
                'usuario_baja' => auth()->user()->name ?? 'Sistema'
            ]);
            
            if ($actualizado) {
                return redirect()
                    ->route('empleados.index')
                    ->with('success', 'Empleado desactivado exitosamente.');
            }
            
            return back()->with('error', 'No se pudo desactivar el empleado.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desactivar empleado: ' . $e->getMessage());
        }
    }
}