<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContratoSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tabla (sin truncate por foreign keys)
        DB::table('Contratos')->delete();

        $contratos = [
            // Juan Carlos González - Admin
            [
                'IDEmpleado' => 1,
                'IDCategoria' => 1, // Gerencial
                'IDCargo' => 1, // Gerente General
                'IDDepartamento' => 1, // Gerencia General
                'NumeroContrato' => 'CONT-2023-0001',
                'TipoContrato' => 'Indefinido',
                'FechaContrato' => '2023-01-15',
                'FechaInicio' => '2023-02-01',
                'FechaFin' => null,
                'HaberBasico' => 15000.00,
                'Estado' => 1
            ],
            
            // María Elena Rodríguez - Manager RRHH
            [
                'IDEmpleado' => 2,
                'IDCategoria' => 2, // Jefatura
                'IDCargo' => 2, // Jefe de RRHH
                'IDDepartamento' => 2, // Recursos Humanos
                'NumeroContrato' => 'CONT-2023-0002',
                'TipoContrato' => 'Indefinido',
                'FechaContrato' => '2023-03-01',
                'FechaInicio' => '2023-03-15',
                'FechaFin' => null,
                'HaberBasico' => 12000.00,
                'Estado' => 1
            ],
            
            // Carlos Alberto Mendoza - Usuario Contabilidad
            [
                'IDEmpleado' => 3,
                'IDCategoria' => 3, // Profesional
                'IDCargo' => 3, // Contador
                'IDDepartamento' => 3, // Finanzas
                'NumeroContrato' => 'CONT-2023-0003',
                'TipoContrato' => 'Indefinido',
                'FechaContrato' => '2023-04-10',
                'FechaInicio' => '2023-04-20',
                'FechaFin' => null,
                'HaberBasico' => 9500.00,
                'Estado' => 1
            ],
            
            // Ana Lucía Vargas - Manager Operaciones
            [
                'IDEmpleado' => 4,
                'IDCategoria' => 2, // Jefatura
                'IDCargo' => 4, // Jefe de Operaciones
                'IDDepartamento' => 4, // Operaciones
                'NumeroContrato' => 'CONT-2023-0004',
                'TipoContrato' => 'Indefinido',
                'FechaContrato' => '2023-05-01',
                'FechaInicio' => '2023-05-15',
                'FechaFin' => null,
                'HaberBasico' => 11500.00,
                'Estado' => 1
            ],
            
            // Roberto Choque - Técnico (inactivo)
            [
                'IDEmpleado' => 5,
                'IDCategoria' => 4, // Técnico
                'IDCargo' => 5, // Técnico de Sistemas
                'IDDepartamento' => 5, // Sistemas
                'NumeroContrato' => 'CONT-2023-0005',
                'TipoContrato' => 'Plazo Fijo',
                'FechaContrato' => '2023-06-01',
                'FechaInicio' => '2023-06-15',
                'FechaFin' => '2024-06-14', // Vencido
                'HaberBasico' => 7500.00,
                'Estado' => 0 // Inactivo
            ],
            
            // Patricia Morales - Técnica Laboratorio
            [
                'IDEmpleado' => 6,
                'IDCategoria' => 4, // Técnico
                'IDCargo' => 6, // Técnico de Laboratorio
                'IDDepartamento' => 6, // Producción
                'NumeroContrato' => 'CONT-2024-0001',
                'TipoContrato' => 'Plazo Fijo',
                'FechaContrato' => '2024-01-10',
                'FechaInicio' => '2024-01-15',
                'FechaFin' => '2025-01-14', // Vigente, vence pronto
                'HaberBasico' => 7000.00,
                'Estado' => 1
            ],
            
            // Luis Fernando Mamani - Analista
            [
                'IDEmpleado' => 7,
                'IDCategoria' => 3, // Profesional
                'IDCargo' => 7, // Analista de Planificación
                'IDDepartamento' => 7, // Planificación
                'NumeroContrato' => 'CONT-2024-0002',
                'TipoContrato' => 'Indefinido',
                'FechaContrato' => '2024-02-01',
                'FechaInicio' => '2024-02-15',
                'FechaFin' => null,
                'HaberBasico' => 8500.00,
                'Estado' => 1
            ],
            
            // Carmen Rosa Ticona - Asistente
            [
                'IDEmpleado' => 8,
                'IDCategoria' => 5, // Administrativo
                'IDCargo' => 8, // Asistente Administrativo
                'IDDepartamento' => 2, // Recursos Humanos
                'NumeroContrato' => 'CONT-2024-0003',
                'TipoContrato' => 'Temporal',
                'FechaContrato' => '2024-06-01',
                'FechaInicio' => '2024-06-15',
                'FechaFin' => '2025-06-14', // Vigente
                'HaberBasico' => 6000.00,
                'Estado' => 1
            ],
            
            // Contrato adicional - Consultoria
            [
                'IDEmpleado' => 1, // Juan Carlos también puede tener consultoria
                'IDCategoria' => 1, // Gerencial
                'IDCargo' => 9, // Consultor Senior
                'IDDepartamento' => 8, // Legal
                'NumeroContrato' => 'CONT-2024-0004',
                'TipoContrato' => 'Consultoria',
                'FechaContrato' => '2024-07-01',
                'FechaInicio' => '2024-07-15',
                'FechaFin' => '2024-12-31', // Por vencer
                'HaberBasico' => 20000.00,
                'Estado' => 1
            ],
            
            // Contrato de practicante
            [
                'IDEmpleado' => 6, // Patricia también puede ser practicante en otro período
                'IDCategoria' => 6, // Practicante
                'IDCargo' => 10, // Practicante
                'IDDepartamento' => 5, // Sistemas
                'NumeroContrato' => 'CONT-2022-0001',
                'TipoContrato' => 'Practicante',
                'FechaContrato' => '2022-08-01',
                'FechaInicio' => '2022-08-15',
                'FechaFin' => '2023-02-14', // Ya finalizado
                'HaberBasico' => 2000.00,
                'Estado' => 1 // Finalizado pero activo en registro
            ]
        ];

        // Insertar uno por uno para manejar IDENTITY correctamente
        foreach ($contratos as $contrato) {
            DB::table('Contratos')->insert($contrato);
        }
    }
}