<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeederAprobado extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = [
            [
                'NombreDepartamento' => 'Recursos Humanos',
                'Descripcion' => 'Gestión del personal y desarrollo humano',
                'Estado' => 1
            ],
            [
                'NombreDepartamento' => 'Tecnologías de la Información',
                'Descripcion' => 'Desarrollo y mantenimiento de sistemas informáticos',
                'Estado' => 1
            ],
            [
                'NombreDepartamento' => 'Operaciones',
                'Descripcion' => 'Supervisión de operaciones hidrocarburíferas',
                'Estado' => 1
            ],
            [
                'NombreDepartamento' => 'Finanzas',
                'Descripcion' => 'Gestión financiera y contable',
                'Estado' => 1
            ],
            [
                'NombreDepartamento' => 'Legal',
                'Descripcion' => 'Asesoría jurídica y contratos',
                'Estado' => 1
            ],
            [
                'NombreDepartamento' => 'Exploración',
                'Descripcion' => 'Exploración de yacimientos hidrocarburíferos',
                'Estado' => 1
            ],
            [
                'NombreDepartamento' => 'Producción',
                'Descripcion' => 'Producción de hidrocarburos',
                'Estado' => 1
            ],
            [
                'NombreDepartamento' => 'Seguridad Industrial',
                'Descripcion' => 'Seguridad ocupacional e industrial',
                'Estado' => 1
            ]
        ];

        DB::table('Departamentos')->insert($departamentos);
    }
}