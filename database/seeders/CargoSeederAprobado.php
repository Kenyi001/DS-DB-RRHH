<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoSeederAprobado extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cargos = [
            [
                'NombreCargo' => 'Gerente General',
                'Descripcion' => 'Máxima autoridad ejecutiva de la empresa',
                'Estado' => 1
            ],
            [
                'NombreCargo' => 'Gerente de RRHH',
                'Descripcion' => 'Responsable de la gestión del talento humano',
                'Estado' => 1
            ],
            [
                'NombreCargo' => 'Gerente de TI',
                'Descripcion' => 'Responsable de tecnologías de información',
                'Estado' => 1
            ],
            [
                'NombreCargo' => 'Desarrollador Senior',
                'Descripcion' => 'Desarrollador de software con experiencia',
                'Estado' => 1
            ],
            [
                'NombreCargo' => 'Desarrollador Junior',
                'Descripcion' => 'Desarrollador de software en formación',
                'Estado' => 1
            ],
            [
                'NombreCargo' => 'Analista de Sistemas',
                'Descripcion' => 'Analista de requerimientos y sistemas',
                'Estado' => 1
            ],
            [
                'NombreCargo' => 'Especialista en RRHH',
                'Descripcion' => 'Especialista en procesos de recursos humanos',
                'Estado' => 1
            ],
            [
                'NombreCargo' => 'Contador',
                'Descripcion' => 'Responsable de contabilidad',
                'Estado' => 1
            ],
            [
                'NombreCargo' => 'Auxiliar Administrativo',
                'Descripcion' => 'Apoyo en tareas administrativas',
                'Estado' => 1
            ],
            [
                'NombreCargo' => 'Ingeniero de Operaciones',
                'Descripcion' => 'Ingeniería en operaciones hidrocarburíferas',
                'Estado' => 1
            ]
        ];

        DB::table('Cargos')->insert($cargos);
    }
}