<?php

namespace App\Modules\Contratos\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Contratos\Services\ContratoService;
use App\Modules\Contratos\Requests\StoreContratoRequest;
use App\Modules\Contratos\Requests\UpdateContratoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratoWebController extends Controller
{
    protected $contratoService;

    public function __construct(ContratoService $contratoService)
    {
        $this->contratoService = $contratoService;
    }

    public function index(Request $request)
    {
        $filtros = $request->only(['search', 'estado', 'tipo', 'empleado_id']);
        $perPage = $request->get('per_page', 20);
        
        try {
            // Obtener contratos paginados
            $contratos = $this->contratoService->listar($filtros, $perPage);
            
            // Obtener empleados para el filtro
            $empleados = DB::table('Empleados')
                           ->where('Estado', 1)
                           ->orderBy('Nombres')
                           ->get();
            
            return view('contratos.index', compact('contratos', 'empleados', 'filtros'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar contratos: ' . $e->getMessage());
        }
    }

    public function create()
    {
        // Obtener datos maestros para el formulario
        $empleados = DB::table('Empleados')
                       ->where('Estado', 1)
                       ->orderBy('Nombres')
                       ->get();

        $cargos = DB::table('Cargos')
                    ->where('Estado', 1)
                    ->orderBy('NombreCargo')
                    ->get();

        $departamentos = DB::table('Departamentos')
                           ->where('Estado', 1)
                           ->orderBy('NombreDepartamento')
                           ->get();

        $categorias = DB::table('Categorias')
                        ->where('Estado', 1)
                        ->orderBy('NombreCategoria')
                        ->get();
        
        return view('contratos.create', compact('empleados', 'cargos', 'departamentos', 'categorias'));
    }

    public function store(StoreContratoRequest $request)
    {
        try {
            $contrato = $this->contratoService->crear($request->validated());
            
            return redirect()
                ->route('contratos.show', $contrato->IDContrato)
                ->with('success', 'Contrato creado exitosamente.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear contrato: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $contrato = $this->contratoService->obtenerPorId($id);
            
            // Obtener planillas del contrato
            $planillas = DB::table('GestionSalarios')
                          ->where('IDContrato', $id)
                          ->orderBy('Gestion', 'desc')
                          ->orderBy('Mes', 'desc')
                          ->limit(12)
                          ->get();
            
            return view('contratos.show', compact('contrato', 'planillas'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar contrato: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $contrato = $this->contratoService->obtenerPorId($id);
            
            // Obtener datos maestros para el formulario
            $empleados = DB::table('Empleados')
                           ->where('Estado', 1)
                           ->orderBy('Nombres')
                           ->get();

            $cargos = DB::table('Cargos')
                        ->where('Estado', 1)
                        ->orderBy('NombreCargo')
                        ->get();

            $departamentos = DB::table('Departamentos')
                               ->where('Estado', 1)
                               ->orderBy('NombreDepartamento')
                               ->get();

            $categorias = DB::table('Categorias')
                            ->where('Estado', 1)
                            ->orderBy('NombreCategoria')
                            ->get();
            
            return view('contratos.edit', compact('contrato', 'empleados', 'cargos', 'departamentos', 'categorias'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar contrato: ' . $e->getMessage());
        }
    }

    public function update(UpdateContratoRequest $request, $id)
    {
        try {
            $actualizado = $this->contratoService->actualizar($id, $request->validated());
            
            if ($actualizado) {
                return redirect()
                    ->route('contratos.show', $id)
                    ->with('success', 'Contrato actualizado exitosamente.');
            }
            
            return back()
                ->withInput()
                ->with('error', 'No se pudo actualizar el contrato.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar contrato: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // Solo desactivar, no eliminar físicamente
            $actualizado = $this->contratoService->actualizar($id, [
                'Estado' => 0,
                'FechaModificacion' => now(),
                'UsuarioModificacion' => auth()->user()->name ?? 'Sistema'
            ]);
            
            if ($actualizado) {
                return redirect()
                    ->route('contratos.index')
                    ->with('success', 'Contrato desactivado exitosamente.');
            }
            
            return back()->with('error', 'No se pudo desactivar el contrato.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desactivar contrato: ' . $e->getMessage());
        }
    }

    public function vigentes(Request $request)
    {
        try {
            // Obtener solo contratos vigentes
            $filtros = ['estado' => 1, 'solo_vigentes' => true];
            $perPage = $request->get('per_page', 20);
            
            $contratos = $this->contratoService->listar($filtros, $perPage);
            
            // Obtener estadísticas completas (no paginadas)
            $totalVigentes = DB::table('Contratos')
                              ->where('Estado', 1)
                              ->where(function($query) {
                                  $query->whereNull('FechaFin')
                                        ->orWhere('FechaFin', '>=', now());
                              })
                              ->count();
                              
            $indefinidos = DB::table('Contratos')
                            ->where('Estado', 1)
                            ->whereNull('FechaFin')
                            ->count();
                            
            $porVencer = DB::table('Contratos')
                          ->where('Estado', 1)
                          ->whereBetween('FechaFin', [now(), now()->addDays(30)])
                          ->count();
                          
            // Debug: verificar si hay contratos con fechas de fin
            $debugInfo = [
                'total_con_fecha_fin' => DB::table('Contratos')
                                           ->where('Estado', 1)
                                           ->whereNotNull('FechaFin')
                                           ->count(),
                'fecha_actual' => now()->format('Y-m-d'),
                'fecha_limite' => now()->addDays(30)->format('Y-m-d'),
                'ejemplos_fechas' => DB::table('Contratos')
                                      ->where('Estado', 1)
                                      ->whereNotNull('FechaFin')
                                      ->select('NumeroContrato', 'FechaFin')
                                      ->orderBy('FechaFin')
                                      ->limit(5)
                                      ->get()
            ];
            
            // Temporal: log para debug
            \Log::info('Debug contratos por vencer', $debugInfo);
                          
            $nominaTotal = DB::table('Contratos')
                            ->where('Estado', 1)
                            ->where(function($query) {
                                $query->whereNull('FechaFin')
                                      ->orWhere('FechaFin', '>=', now());
                            })
                            ->sum('HaberBasico');
            
            $stats = [
                'total_vigentes' => $totalVigentes,
                'indefinidos' => $indefinidos,
                'por_vencer' => $porVencer,
                'nomina_total' => $nominaTotal,
                'debug' => $debugInfo
            ];
            
            return view('contratos.vigentes', compact('contratos', 'stats'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar contratos vigentes: ' . $e->getMessage());
        }
    }

    public function alertas(Request $request)
    {
        try {
            // Contratos que vencen pronto
            $contratosPorVencer = DB::table('Contratos')
                                    ->join('Empleados', 'Contratos.IDEmpleado', '=', 'Empleados.IDEmpleado')
                                    ->join('Cargos', 'Contratos.IDCargo', '=', 'Cargos.IDCargo')
                                    ->join('Departamentos', 'Contratos.IDDepartamento', '=', 'Departamentos.IDDepartamento')
                                    ->where('Contratos.Estado', 1)
                                    ->whereBetween('Contratos.FechaFin', [
                                        now(),
                                        now()->addDays(30)
                                    ])
                                    ->select(
                                        'Contratos.*',
                                        'Empleados.Nombres',
                                        'Empleados.ApellidoPaterno',
                                        'Empleados.ApellidoMaterno',
                                        'Cargos.NombreCargo',
                                        'Departamentos.NombreDepartamento'
                                    )
                                    ->orderBy('Contratos.FechaFin')
                                    ->get();

            // Contratos sin fecha de fin (indefinidos)
            $contratosIndefinidos = DB::table('Contratos')
                                      ->join('Empleados', 'Contratos.IDEmpleado', '=', 'Empleados.IDEmpleado')
                                      ->join('Cargos', 'Contratos.IDCargo', '=', 'Cargos.IDCargo')
                                      ->join('Departamentos', 'Contratos.IDDepartamento', '=', 'Departamentos.IDDepartamento')
                                      ->where('Contratos.Estado', 1)
                                      ->whereNull('Contratos.FechaFin')
                                      ->select(
                                          'Contratos.*',
                                          'Empleados.Nombres',
                                          'Empleados.ApellidoPaterno',
                                          'Empleados.ApellidoMaterno',
                                          'Cargos.NombreCargo',
                                          'Departamentos.NombreDepartamento'
                                      )
                                      ->orderBy('Contratos.FechaInicio', 'desc')
                                      ->get();
            
            return view('contratos.alertas', compact('contratosPorVencer', 'contratosIndefinidos'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar alertas de contratos: ' . $e->getMessage());
        }
    }
}