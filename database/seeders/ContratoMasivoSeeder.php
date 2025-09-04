<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContratoMasivoSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar contratos existentes excepto los primeros 10 (datos base)
        DB::table('Contratos')->where('IDContrato', '>', 10)->delete();
        
        // Obtener empleados (desde el 9 hasta el 308)
        $empleados = DB::table('Empleados')
                      ->where('IDEmpleado', '>', 8)
                      ->where('Estado', 1) // Solo empleados activos
                      ->get(['IDEmpleado', 'FechaIngreso']);

        // Obtener datos maestros
        $categorias = DB::table('Categorias')->pluck('IDCategoria')->toArray();
        $cargos = DB::table('Cargos')->pluck('IDCargo')->toArray(); 
        $departamentos = DB::table('Departamentos')->pluck('IDDepartamento')->toArray();

        $contratos = [];
        $numeroContrato = 5; // Continuar numeración

        foreach ($empleados as $empleado) {
            // Distribución de tipos de contrato realista
            $tipoContrato = $this->determinarTipoContrato();
            
            // Calcular fechas del contrato
            $fechaIngreso = Carbon::parse($empleado->FechaIngreso);
            $fechaContrato = $fechaIngreso->subDays(rand(1, 15))->format('Y-m-d');
            $fechaInicio = $empleado->FechaIngreso;
            
            // Determinar fecha fin según tipo
            $fechaFin = null;
            if ($tipoContrato === 'Plazo Fijo') {
                $fechaFin = Carbon::parse($fechaInicio)->addYear()->format('Y-m-d');
            } elseif ($tipoContrato === 'Temporal') {
                $fechaFin = Carbon::parse($fechaInicio)->addMonths(6)->format('Y-m-d');
            } elseif ($tipoContrato === 'Consultoria') {
                $fechaFin = Carbon::parse($fechaInicio)->addMonths(3)->format('Y-m-d');
            } elseif ($tipoContrato === 'Practicante') {
                $fechaFin = Carbon::parse($fechaInicio)->addMonths(4)->format('Y-m-d');
            }

            // Determinar categoría y haber básico
            $categoria = $this->determinarCategoria();
            $haberBasico = $this->calcularHaberBasico($categoria, $tipoContrato);
            
            // Estado del contrato (90% activos)
            $estado = rand(1, 100) <= 90 ? 1 : 0;

            $contratos[] = [
                'IDEmpleado' => $empleado->IDEmpleado,
                'IDCategoria' => $categoria,
                'IDCargo' => $cargos[array_rand($cargos)],
                'IDDepartamento' => $departamentos[array_rand($departamentos)],
                'NumeroContrato' => $this->generarNumeroContrato($numeroContrato),
                'TipoContrato' => $tipoContrato,
                'FechaContrato' => $fechaContrato,
                'FechaInicio' => $fechaInicio,
                'FechaFin' => $fechaFin,
                'HaberBasico' => $haberBasico,
                'Estado' => $estado
            ];

            $numeroContrato++;
        }

        // Insertar en lotes de 50
        $chunks = array_chunk($contratos, 50);
        foreach ($chunks as $chunk) {
            DB::table('Contratos')->insert($chunk);
        }

        $this->command->info('✅ Generados ' . count($contratos) . ' contratos masivos exitosamente');
    }

    private function determinarTipoContrato(): string
    {
        $probabilidades = [
            'Indefinido' => 60,    // 60%
            'Plazo Fijo' => 25,    // 25% 
            'Temporal' => 8,       // 8%
            'Consultoria' => 4,    // 4%
            'Practicante' => 3     // 3%
        ];

        $rand = rand(1, 100);
        $acumulado = 0;

        foreach ($probabilidades as $tipo => $prob) {
            $acumulado += $prob;
            if ($rand <= $acumulado) {
                return $tipo;
            }
        }

        return 'Indefinido';
    }

    private function determinarCategoria(): int
    {
        $probabilidades = [
            1 => 5,   // Categoría A (Gerencial) - 5%
            2 => 15,  // Categoría B (Jefatura) - 15%
            3 => 35,  // Categoría C (Profesional) - 35%
            4 => 25,  // Categoría D (Técnico) - 25%
            5 => 15,  // Categoría E (Administrativo) - 15%
            6 => 5    // Categoría F (Practicante) - 5%
        ];

        $rand = rand(1, 100);
        $acumulado = 0;

        foreach ($probabilidades as $categoria => $prob) {
            $acumulado += $prob;
            if ($rand <= $acumulado) {
                return $categoria;
            }
        }

        return 3; // Default: Profesional
    }

    private function calcularHaberBasico(int $categoria, string $tipoContrato): float
    {
        $haberesBase = [
            1 => [12000, 25000], // Gerencial
            2 => [8000, 15000],  // Jefatura  
            3 => [5000, 12000],  // Profesional
            4 => [3500, 8000],   // Técnico
            5 => [2500, 5000],   // Administrativo
            6 => [1500, 3000]    // Practicante
        ];

        $rango = $haberesBase[$categoria];
        $haberBase = rand($rango[0], $rango[1]);

        // Ajustes por tipo de contrato
        $multiplicadores = [
            'Indefinido' => 1.0,
            'Plazo Fijo' => 0.95,
            'Temporal' => 0.9,
            'Consultoria' => 1.2,
            'Practicante' => 0.7
        ];

        return round($haberBase * $multiplicadores[$tipoContrato], 2);
    }

    private function generarNumeroContrato(int $numero): string
    {
        $año = rand(2020, 2025);
        return sprintf("CONT-%d-%04d", $año, $numero);
    }
}