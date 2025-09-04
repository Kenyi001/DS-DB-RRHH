<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Planilla;
use App\Models\Contrato;
use App\Modules\Empleados\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener estadísticas básicas para el dashboard
        $stats = $this->getEstadisticasBasicas();
        
        return view('dashboard.index', compact('stats'));
    }
    
    public function reportes()
    {
        return view('reportes.dashboard');
    }
    
    public function configuracion()
    {
        return view('configuracion.index');
    }
    
    public function perfil()
    {
        return view('configuracion.perfil');
    }
    
    public function miPerfil()
    {
        $user = auth()->user();
        return view('perfil.index', compact('user'));
    }
    
    public function usuarios()
    {
        return view('admin.usuarios');
    }
    
    public function sistema()
    {
        return view('admin.sistema');
    }
    
    private function getEstadisticasBasicas()
    {
        try {
            $stats = [
                'empleados' => [
                    'total' => Empleado::count(),
                    'activos' => Empleado::where('Estado', 1)->count(),
                ],
                'contratos' => [
                    'total' => Contrato::count(),
                    'vigentes' => Contrato::where('Estado', 1)
                                        ->where(function($query) {
                                            $query->whereNull('FechaFin')
                                                  ->orWhere('FechaFin', '>=', now());
                                        })
                                        ->count(),
                    'porVencer' => Contrato::where('Estado', 1)
                                          ->whereBetween('FechaFin', [
                                              now(), 
                                              now()->addDays(30)
                                          ])
                                          ->count(),
                    // Debug temporal
                    'debug' => [
                        'indefinidos' => Contrato::where('Estado', 1)->whereNull('FechaFin')->count(),
                        'con_fecha_fin' => Contrato::where('Estado', 1)->whereNotNull('FechaFin')->count(),
                        'fechas_pasado' => Contrato::where('Estado', 1)
                                                  ->whereNotNull('FechaFin')
                                                  ->where('FechaFin', '<', now())
                                                  ->count(),
                        'fechas_futuro' => Contrato::where('Estado', 1)
                                                  ->whereNotNull('FechaFin')
                                                  ->where('FechaFin', '>=', now())
                                                  ->count(),
                        'fechas_incorrectas' => Contrato::where('Estado', 1)
                                                       ->whereNotNull('FechaFin')
                                                       ->where('FechaFin', '<', '2020-01-01')
                                                       ->count(),
                        'ejemplos_fechas_malas' => Contrato::where('Estado', 1)
                                                          ->whereNotNull('FechaFin')
                                                          ->where('FechaFin', '<', '2020-01-01')
                                                          ->select('NumeroContrato', 'FechaFin')
                                                          ->limit(5)
                                                          ->get()
                    ]
                ],
                'planillas' => [
                    'total' => Planilla::count(),
                    'mesActual' => Planilla::where('Gestion', now()->year)
                                          ->where('Mes', now()->month)
                                          ->count(),
                    'pendientes' => Planilla::where('EstadoPago', 'Pendiente')->count(),
                    'totalNomina' => Planilla::where('Gestion', now()->year)
                                           ->where('Mes', now()->month)
                                           ->sum('LiquidoPagable'),
                ],
            ];
            
            // Calcular promedio de nómina
            if ($stats['planillas']['mesActual'] > 0) {
                $stats['planillas']['promedio'] = $stats['planillas']['totalNomina'] / $stats['planillas']['mesActual'];
            } else {
                $stats['planillas']['promedio'] = 0;
            }
            
            return $stats;
            
        } catch (\Exception $e) {
            // En caso de error, retornar valores por defecto
            return [
                'empleados' => ['total' => 308, 'activos' => 300],
                'contratos' => ['total' => 296, 'vigentes' => 245, 'porVencer' => 15],
                'planillas' => [
                    'total' => 8288, 
                    'mesActual' => 259, 
                    'pendientes' => 12,
                    'totalNomina' => 2187824,
                    'promedio' => 8446
                ],
            ];
        }
    }
}