<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
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
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Tecnologías de Información',
                'Descripcion' => 'Desarrollo y mantenimiento de sistemas informáticos',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Operaciones',
                'Descripcion' => 'Gestión de operaciones y procesos productivos',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Finanzas',
                'Descripcion' => 'Control financiero y contabilidad',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Exploración y Producción',
                'Descripcion' => 'Actividades de exploración y extracción',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Refinación',
                'Descripcion' => 'Procesos de refinación de hidrocarburos',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Legal',
                'Descripcion' => 'Asesoría jurídica y legal',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Comercialización',
                'Descripcion' => 'Marketing y ventas de productos',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ]
        ];

        DB::table('departamentos')->insert($departamentos);
    }
}