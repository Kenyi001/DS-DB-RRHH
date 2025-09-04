<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Planilla;

class PlanillaMasivaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar planillas existentes
        DB::table('GestionSalarios')->delete();
        
        // Obtener contratos activos
        $contratos = DB::table('Contratos')
                      ->where('Estado', 1)
                      ->get(['IDContrato', 'HaberBasico', 'TipoContrato']);

        $planillas = [];
        $gestiones = [2023, 2024, 2025];
        
        foreach ($contratos as $contrato) {
            foreach ($gestiones as $gestion) {
                // Generar planillas para algunos meses del año (6-12 meses)
                $mesesGenerar = $gestion == 2025 ? range(1, 8) : range(1, 12);
                
                foreach ($mesesGenerar as $mes) {
                    // Distribución realista de días trabajados
                    $diasTrabajados = $this->generarDiasTrabajados($mes, $gestion, $contrato->TipoContrato);
                    
                    // Crear planilla temporal para calcular totales
                    $planillaTemp = new Planilla([
                        'IDContrato' => $contrato->IDContrato,
                        'Mes' => $mes,
                        'Gestion' => $gestion,
                        'DiasTrabajos' => $diasTrabajados,
                        'SalarioBasico' => $contrato->HaberBasico,
                        'EstadoPago' => 'Pendiente'
                    ]);

                    // Calcular totales usando el método del modelo
                    $calculos = $planillaTemp->calcularTotales();
                    
                    // Estado de pago realista
                    $estadoPago = $this->determinarEstadoPago($gestion, $mes);
                    $fechaPago = null;
                    
                    if ($estadoPago === 'Pagado') {
                        $fechaPago = Carbon::create($gestion, $mes, rand(25, 30))->format('Y-m-d');
                    }

                    $planillas[] = [
                        'IDContrato' => $contrato->IDContrato,
                        'Mes' => $mes,
                        'Gestion' => $gestion,
                        'DiasTrabajos' => $diasTrabajados,
                        'SalarioBasico' => $contrato->HaberBasico,
                        'TotalIngresos' => $calculos['TotalIngresos'],
                        'TotalDescuentos' => $calculos['TotalDescuentos'],
                        'LiquidoPagable' => $calculos['LiquidoPagable'],
                        'FechaPago' => $fechaPago,
                        'EstadoPago' => $estadoPago
                    ];
                }
            }
        }

        // Insertar en lotes de 100 para mejor rendimiento
        $chunks = array_chunk($planillas, 100);
        foreach ($chunks as $chunk) {
            DB::table('GestionSalarios')->insert($chunk);
        }

        $this->command->info('✅ Generadas ' . count($planillas) . ' planillas masivas exitosamente');
        $this->command->info('📊 Distribución por estado:');
        
        // Mostrar estadísticas
        $stats = collect($planillas)->groupBy('EstadoPago')->map->count();
        foreach ($stats as $estado => $cantidad) {
            $this->command->info("   {$estado}: {$cantidad}");
        }
    }

    private function generarDiasTrabajados(int $mes, int $gestion, string $tipoContrato): int
    {
        // Días base del mes
        $diasDelMes = Carbon::create($gestion, $mes)->daysInMonth;
        
        // Ajustar según tipo de contrato
        $multiplicador = match($tipoContrato) {
            'Indefinido' => 1.0,
            'Plazo Fijo' => 1.0,
            'Temporal' => 0.95,    // Pueden tener algunos días menos
            'Consultoria' => 0.8,  // Trabajo por proyectos
            'Practicante' => 0.9   // Pueden faltar ocasionalmente
        };

        // Variación aleatoria realista (95-100% del tiempo)
        $factor = rand(95, 100) / 100;
        $diasCalculados = $diasDelMes * $multiplicador * $factor;
        
        return min($diasDelMes, max(1, round($diasCalculados)));
    }

    private function determinarEstadoPago(int $gestion, int $mes): string
    {
        $fechaActual = Carbon::now();
        $fechaPlanilla = Carbon::create($gestion, $mes, 1);
        
        // Planillas del futuro son pendientes
        if ($fechaPlanilla->isFuture()) {
            return 'Pendiente';
        }
        
        // Planillas muy antiguas (más de 2 años) están pagadas
        if ($fechaPlanilla->diffInMonths($fechaActual) > 24) {
            return rand(1, 100) <= 95 ? 'Pagado' : 'Anulado';
        }
        
        // Planillas del último año
        if ($fechaPlanilla->diffInMonths($fechaActual) <= 12) {
            $probabilidades = [
                'Pagado' => 80,
                'Pendiente' => 18,
                'Anulado' => 2
            ];
        } else {
            // Planillas de 1-2 años atrás
            $probabilidades = [
                'Pagado' => 90,
                'Pendiente' => 8,
                'Anulado' => 2
            ];
        }

        $rand = rand(1, 100);
        $acumulado = 0;

        foreach ($probabilidades as $estado => $prob) {
            $acumulado += $prob;
            if ($rand <= $acumulado) {
                return $estado;
            }
        }

        return 'Pagado';
    }
}