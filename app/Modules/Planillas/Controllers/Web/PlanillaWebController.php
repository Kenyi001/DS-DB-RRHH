<?php

namespace App\Modules\Planillas\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Planillas\Services\PlanillaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlanillaWebController extends Controller
{
    protected $planillaService;

    public function __construct(PlanillaService $planillaService)
    {
        $this->planillaService = $planillaService;
    }

    public function index(Request $request)
    {
        $filtros = $request->only(['search', 'gestion', 'mes', 'estado_pago', 'empleado_id']);
        $perPage = $request->get('per_page', 20);
        
        // Valores por defecto para filtros
        if (!isset($filtros['gestion']) || empty($filtros['gestion'])) {
            $filtros['gestion'] = now()->year;
        }
        
        try {
            // Obtener planillas paginadas
            $planillas = $this->planillaService->listar($filtros, $perPage);
            
            // Obtener empleados para el filtro
            $empleados = DB::table('Empleados')
                           ->where('Estado', 1)
                           ->orderBy('Nombres')
                           ->get();
            
            // Obtener años disponibles
            $gestiones = DB::table('GestionSalarios')
                           ->select('Gestion')
                           ->distinct()
                           ->orderBy('Gestion', 'desc')
                           ->pluck('Gestion');
            
            // Estadísticas del período actual
            $stats = $this->getEstadisticasPeriodo($filtros['gestion'], $filtros['mes'] ?? null);
            
            return view('planillas.index', compact('planillas', 'empleados', 'gestiones', 'filtros', 'stats'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar planillas: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $planilla = $this->planillaService->obtenerPorId($id);
            
            // Obtener información del empleado y contrato
            $detalle = DB::table('GestionSalarios as gs')
                         ->join('Contratos as c', 'gs.IDContrato', '=', 'c.IDContrato')
                         ->join('Empleados as e', 'c.IDEmpleado', '=', 'e.IDEmpleado')
                         ->join('Cargos as car', 'c.IDCargo', '=', 'car.IDCargo')
                         ->join('Departamentos as d', 'c.IDDepartamento', '=', 'd.IDDepartamento')
                         ->join('Categorias as cat', 'c.IDCategoria', '=', 'cat.IDCategoria')
                         ->where('gs.IDGestionSalario', $id)
                         ->select(
                             'gs.*',
                             'e.Nombres',
                             'e.ApellidoPaterno',
                             'e.ApellidoMaterno',
                             'e.CI',
                             'car.NombreCargo',
                             'd.NombreDepartamento',
                             'cat.NombreCategoria',
                             'c.HaberBasico as SalarioContrato'
                         )
                         ->first();
            
            return view('planillas.show', compact('planilla', 'detalle'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar planilla: ' . $e->getMessage());
        }
    }

    public function generar()
    {
        // Obtener datos para el formulario
        $empleadosActivos = DB::table('Empleados as e')
                              ->join('Contratos as c', 'e.IDEmpleado', '=', 'c.IDEmpleado')
                              ->join('Cargos as car', 'c.IDCargo', '=', 'car.IDCargo')
                              ->join('Departamentos as d', 'c.IDDepartamento', '=', 'd.IDDepartamento')
                              ->where('e.Estado', 1)
                              ->where('c.Estado', 1)
                              ->where(function($query) {
                                  $query->whereNull('c.FechaFin')
                                        ->orWhere('c.FechaFin', '>=', now());
                              })
                              ->select(
                                  'e.*',
                                  'c.IDContrato',
                                  'c.HaberBasico',
                                  'car.NombreCargo',
                                  'd.NombreDepartamento'
                              )
                              ->orderBy('e.Nombres')
                              ->get();

        $departamentos = DB::table('Departamentos')
                           ->where('Estado', 1)
                           ->orderBy('NombreDepartamento')
                           ->get();

        // Verificar si ya existe planilla para el mes actual
        $mesActual = now()->month;
        $gestionActual = now()->year;
        
        $planillasExistentes = DB::table('GestionSalarios')
                                 ->where('Mes', $mesActual)
                                 ->where('Gestion', $gestionActual)
                                 ->count();

        return view('planillas.generar', compact('empleadosActivos', 'departamentos', 'mesActual', 'gestionActual', 'planillasExistentes'));
    }

    public function procesarGeneracion(Request $request)
    {
        $request->validate([
            'mes' => 'required|integer|min:1|max:12',
            'gestion' => 'required|integer|min:2020|max:' . (now()->year + 1),
            'empleados' => 'required|array|min:1',
            'empleados.*' => 'exists:Empleados,IDEmpleado'
        ]);

        try {
            DB::beginTransaction();

            $mes = $request->mes;
            $gestion = $request->gestion;
            $empleadosSeleccionados = $request->empleados;
            $planillasGeneradas = 0;

            foreach ($empleadosSeleccionados as $empleadoId) {
                // Verificar que no existe ya una planilla para este empleado en el período
                $existe = DB::table('GestionSalarios as gs')
                            ->join('Contratos as c', 'gs.IDContrato', '=', 'c.IDContrato')
                            ->where('c.IDEmpleado', $empleadoId)
                            ->where('gs.Mes', $mes)
                            ->where('gs.Gestion', $gestion)
                            ->exists();

                if ($existe) {
                    continue; // Saltar si ya existe
                }

                // Obtener el contrato activo del empleado
                $contrato = DB::table('Contratos')
                              ->where('IDEmpleado', $empleadoId)
                              ->where('Estado', 1)
                              ->where(function($query) {
                                  $query->whereNull('FechaFin')
                                        ->orWhere('FechaFin', '>=', now());
                              })
                              ->orderBy('FechaInicio', 'desc')
                              ->first();

                if (!$contrato) {
                    continue; // Saltar si no tiene contrato activo
                }

                // Generar la planilla usando el servicio
                $planilla = $this->planillaService->generarPlanillaMensual(
                    $contrato->IDContrato,
                    $mes,
                    $gestion
                );

                if ($planilla) {
                    $planillasGeneradas++;
                }
            }

            DB::commit();

            return redirect()
                ->route('planillas.index', ['gestion' => $gestion, 'mes' => $mes])
                ->with('success', "Se generaron {$planillasGeneradas} planillas exitosamente para {$mes}/{$gestion}.");

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withInput()
                ->with('error', 'Error al generar planillas: ' . $e->getMessage());
        }
    }

    public function reportes(Request $request)
    {
        $filtros = $request->only(['gestion', 'mes', 'departamento_id', 'tipo_reporte']);
        
        // Valores por defecto - buscar el período con datos más reciente
        if (!isset($filtros['gestion']) || empty($filtros['gestion'])) {
            $ultimoPeriodo = DB::table('GestionSalarios')
                              ->select('Gestion', 'Mes')
                              ->orderBy('Gestion', 'desc')
                              ->orderBy('Mes', 'desc')
                              ->first();
                              
            $filtros['gestion'] = $ultimoPeriodo ? $ultimoPeriodo->Gestion : now()->year;
        }
        
        if (!isset($filtros['mes']) || empty($filtros['mes'])) {
            $ultimoPeriodo = DB::table('GestionSalarios')
                              ->where('Gestion', $filtros['gestion'])
                              ->select('Mes')
                              ->orderBy('Mes', 'desc')
                              ->first();
                              
            $filtros['mes'] = $ultimoPeriodo ? $ultimoPeriodo->Mes : now()->month;
        }

        try {
            $reportes = [];
            
            // Debug temporal: obtener períodos disponibles
            $periodosDisponibles = DB::table('GestionSalarios')
                                    ->select('Gestion', 'Mes', DB::raw('COUNT(*) as cantidad'))
                                    ->groupBy('Gestion', 'Mes')
                                    ->orderBy('Gestion', 'desc')
                                    ->orderBy('Mes', 'desc')
                                    ->limit(10)
                                    ->get();
            
            $reportes['debug'] = [
                'filtros_aplicados' => $filtros,
                'periodos_disponibles' => $periodosDisponibles,
                'total_planillas_sistema' => DB::table('GestionSalarios')->count()
            ];

            // Reporte por departamento
            if (!isset($filtros['tipo_reporte']) || $filtros['tipo_reporte'] === 'departamento') {
                $reportes['departamentos'] = DB::table('GestionSalarios as gs')
                    ->join('Contratos as c', 'gs.IDContrato', '=', 'c.IDContrato')
                    ->join('Empleados as e', 'c.IDEmpleado', '=', 'e.IDEmpleado')
                    ->join('Departamentos as d', 'c.IDDepartamento', '=', 'd.IDDepartamento')
                    ->where('gs.Gestion', $filtros['gestion'])
                    ->where('gs.Mes', $filtros['mes'])
                    ->when(isset($filtros['departamento_id']) && !empty($filtros['departamento_id']), function($query) use ($filtros) {
                        return $query->where('d.IDDepartamento', $filtros['departamento_id']);
                    })
                    ->selectRaw('
                        d.NombreDepartamento,
                        COUNT(*) as TotalEmpleados,
                        SUM(gs.SalarioBasico) as TotalSalarios,
                        SUM(gs.TotalIngresos) as TotalIngresos,
                        SUM(gs.TotalDescuentos) as TotalDescuentos,
                        SUM(gs.LiquidoPagable) as TotalLiquido,
                        AVG(gs.LiquidoPagable) as PromedioLiquido
                    ')
                    ->groupBy('d.IDDepartamento', 'd.NombreDepartamento')
                    ->orderBy('TotalLiquido', 'desc')
                    ->get();
            }

            // Reporte de totales generales
            $reportes['totales'] = DB::table('GestionSalarios as gs')
                ->join('Contratos as c', 'gs.IDContrato', '=', 'c.IDContrato')
                ->where('gs.Gestion', $filtros['gestion'])
                ->where('gs.Mes', $filtros['mes'])
                ->selectRaw("
                    COUNT(*) as TotalPlanillas,
                    SUM(gs.SalarioBasico) as TotalSalarios,
                    SUM(gs.TotalIngresos) as TotalIngresos,
                    SUM(gs.TotalDescuentos) as TotalDescuentos,
                    SUM(gs.LiquidoPagable) as TotalLiquido,
                    COUNT(CASE WHEN gs.EstadoPago = 'Pagado' THEN 1 END) as PlanillasPagadas,
                    COUNT(CASE WHEN gs.EstadoPago = 'Pendiente' THEN 1 END) as PlanillasPendientes
                ")
                ->first();

            // Top 10 empleados con mayor sueldo
            if (!isset($filtros['tipo_reporte']) || $filtros['tipo_reporte'] === 'top_sueldos') {
                $reportes['topSueldos'] = DB::table('GestionSalarios as gs')
                    ->join('Contratos as c', 'gs.IDContrato', '=', 'c.IDContrato')
                    ->join('Empleados as e', 'c.IDEmpleado', '=', 'e.IDEmpleado')
                    ->join('Cargos as car', 'c.IDCargo', '=', 'car.IDCargo')
                    ->join('Departamentos as d', 'c.IDDepartamento', '=', 'd.IDDepartamento')
                    ->where('gs.Gestion', $filtros['gestion'])
                    ->where('gs.Mes', $filtros['mes'])
                    ->select(
                        'e.Nombres',
                        'e.ApellidoPaterno',
                        'e.ApellidoMaterno',
                        'car.NombreCargo',
                        'd.NombreDepartamento',
                        'gs.LiquidoPagable'
                    )
                    ->orderBy('gs.LiquidoPagable', 'desc')
                    ->limit(10)
                    ->get();
            }

            $departamentos = DB::table('Departamentos')
                               ->where('Estado', 1)
                               ->orderBy('NombreDepartamento')
                               ->get();

            $gestiones = DB::table('GestionSalarios')
                           ->select('Gestion')
                           ->distinct()
                           ->orderBy('Gestion', 'desc')
                           ->pluck('Gestion');

            return view('planillas.reportes', compact('reportes', 'filtros', 'departamentos', 'gestiones'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar reportes: ' . $e->getMessage());
        }
    }

    private function getEstadisticasPeriodo($gestion, $mes = null)
    {
        try {
            $query = DB::table('GestionSalarios')
                       ->where('Gestion', $gestion);

            if ($mes) {
                $query->where('Mes', $mes);
            }

            $stats = $query->selectRaw("
                COUNT(*) as total_planillas,
                SUM(LiquidoPagable) as total_nomina,
                AVG(LiquidoPagable) as promedio_sueldo,
                COUNT(CASE WHEN EstadoPago = 'Pagado' THEN 1 END) as pagadas,
                COUNT(CASE WHEN EstadoPago = 'Pendiente' THEN 1 END) as pendientes
            ")->first();

            return [
                'total_planillas' => $stats->total_planillas ?? 0,
                'total_nomina' => $stats->total_nomina ?? 0,
                'promedio_sueldo' => $stats->promedio_sueldo ?? 0,
                'pagadas' => $stats->pagadas ?? 0,
                'pendientes' => $stats->pendientes ?? 0
            ];

        } catch (\Exception $e) {
            return [
                'total_planillas' => 0,
                'total_nomina' => 0,
                'promedio_sueldo' => 0,
                'pagadas' => 0,
                'pendientes' => 0
            ];
        }
    }
}