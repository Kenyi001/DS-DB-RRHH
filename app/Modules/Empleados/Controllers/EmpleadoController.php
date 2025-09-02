<?php

namespace App\Modules\Empleados\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Empleados\Services\EmpleadoService;
use App\Modules\Empleados\Requests\StoreEmpleadoRequest;
use App\Modules\Empleados\Requests\UpdateEmpleadoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class EmpleadoController extends Controller
{
    protected $empleadoService;

    public function __construct(EmpleadoService $empleadoService)
    {
        $this->empleadoService = $empleadoService;
        
        // TODO: Implementar middleware de autenticación y autorización
        // $this->middleware('auth');
        // $this->middleware('can:empleados.view')->only(['index', 'show']);
        // $this->middleware('can:empleados.create')->only(['create', 'store']);
        // $this->middleware('can:empleados.edit')->only(['edit', 'update']);
        // $this->middleware('can:empleados.delete')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        try {
            $filtros = $request->only([
                'buscar', 'estado', 'genero', 'ciudad', 
                'fecha_ingreso_desde', 'fecha_ingreso_hasta',
                'order_by', 'order_direction'
            ]);

            $perPage = $request->get('per_page', 15);
            
            $empleados = $this->empleadoService->listar($filtros, $perPage);
            $estadisticas = $this->empleadoService->obtenerEstadisticas();

            return view('empleados.index', compact('empleados', 'estadisticas', 'filtros'));

        } catch (Exception $e) {
            return back()->with('error', 'Error al cargar empleados: ' . $e->getMessage());
        }
    }

    public function create(): View
    {
        $estados = ['Activo', 'Inactivo', 'Vacaciones', 'Licencia'];
        $generos = ['M' => 'Masculino', 'F' => 'Femenino'];
        $estadosCiviles = ['Soltero', 'Casado', 'Divorciado', 'Viudo'];

        return view('empleados.create', compact('estados', 'generos', 'estadosCiviles'));
    }

    public function store(StoreEmpleadoRequest $request): RedirectResponse
    {
        try {
            $empleado = $this->empleadoService->crear($request->validated());

            return redirect()
                ->route('empleados.show', $empleado->id)
                ->with('success', "Empleado {$empleado->nombre_completo} creado exitosamente.");

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear empleado: ' . $e->getMessage());
        }
    }

    public function show(int $id): View
    {
        try {
            $empleado = $this->empleadoService->obtenerPorId($id);

            return view('empleados.show', compact('empleado'));

        } catch (Exception $e) {
            return back()->with('error', 'Error al mostrar empleado: ' . $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        try {
            $empleado = $this->empleadoService->obtenerPorId($id);
            
            $estados = ['Activo', 'Inactivo', 'Vacaciones', 'Licencia'];
            $generos = ['M' => 'Masculino', 'F' => 'Femenino'];
            $estadosCiviles = ['Soltero', 'Casado', 'Divorciado', 'Viudo'];

            return view('empleados.edit', compact('empleado', 'estados', 'generos', 'estadosCiviles'));

        } catch (Exception $e) {
            return back()->with('error', 'Error al cargar empleado: ' . $e->getMessage());
        }
    }

    public function update(UpdateEmpleadoRequest $request, int $id): RedirectResponse
    {
        try {
            $this->empleadoService->actualizar($id, $request->validated());

            return redirect()
                ->route('empleados.show', $id)
                ->with('success', 'Empleado actualizado exitosamente.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar empleado: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        try {
            $motivo = $request->get('motivo', 'Baja solicitada desde sistema');
            $usuario = auth()->user()->name ?? 'Sistema'; // TODO: Implementar auth

            $this->empleadoService->eliminar($id, $motivo, $usuario);

            return redirect()
                ->route('empleados.index')
                ->with('success', 'Empleado marcado como baja exitosamente.');

        } catch (Exception $e) {
            return back()->with('error', 'Error al dar de baja empleado: ' . $e->getMessage());
        }
    }

    public function reactivar(int $id): RedirectResponse
    {
        try {
            $this->empleadoService->reactivar($id);

            return redirect()
                ->route('empleados.show', $id)
                ->with('success', 'Empleado reactivado exitosamente.');

        } catch (Exception $e) {
            return back()->with('error', 'Error al reactivar empleado: ' . $e->getMessage());
        }
    }

    public function buscar(Request $request)
    {
        try {
            $termino = $request->get('q', '');
            $limite = $request->get('limit', 10);

            $empleados = $this->empleadoService->buscarParaSelect($termino, $limite);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $empleados->map(function ($empleado) {
                        return [
                            'id' => $empleado->id,
                            'text' => "{$empleado->nombre_completo} - CI: {$empleado->ci}",
                            'codigo' => $empleado->codigo_empleado,
                            'ci' => $empleado->ci
                        ];
                    })
                ]);
            }

            return view('empleados.partials.search-results', compact('empleados'));

        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la búsqueda: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error en la búsqueda: ' . $e->getMessage());
        }
    }

    public function reporte(Request $request)
    {
        try {
            $filtros = $request->only([
                'buscar', 'estado', 'genero', 'ciudad',
                'fecha_ingreso_desde', 'fecha_ingreso_hasta'
            ]);

            $formato = $request->get('formato', 'pdf');
            $reporte = $this->empleadoService->generarReporteEmpleados($filtros);

            switch ($formato) {
                case 'excel':
                    // TODO: Implementar exportación a Excel
                    return $this->exportarExcel($reporte);
                    
                case 'pdf':
                    // TODO: Implementar exportación a PDF
                    return $this->exportarPdf($reporte);
                    
                default:
                    return response()->json($reporte);
            }

        } catch (Exception $e) {
            return back()->with('error', 'Error generando reporte: ' . $e->getMessage());
        }
    }

    // TODO: Implementar métodos de exportación
    private function exportarExcel($reporte)
    {
        // Implementar exportación a Excel usando Laravel Excel
        return response()->json(['message' => 'Exportación Excel pendiente de implementar']);
    }

    private function exportarPdf($reporte)
    {
        // Implementar exportación a PDF usando DomPDF o similar
        return response()->json(['message' => 'Exportación PDF pendiente de implementar']);
    }
}