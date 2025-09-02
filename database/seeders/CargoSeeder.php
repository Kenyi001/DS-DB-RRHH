<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cargos = [
            // Cargos directivos
            [
                'Nombre' => 'Gerente General',
                'Descripcion' => 'Máximo responsable de la gestión empresarial',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Gerente de Recursos Humanos',
                'Descripcion' => 'Responsable de la gestión del talento humano',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Gerente de TI',
                'Descripcion' => 'Responsable de las tecnologías de información',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Gerente de Operaciones',
                'Descripcion' => 'Responsable de las operaciones productivas',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Gerente Financiero',
                'Descripcion' => 'Responsable de la gestión financiera',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            // Cargos técnicos
            [
                'Nombre' => 'Analista de RRHH',
                'Descripcion' => 'Análisis y gestión de procesos de recursos humanos',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Desarrollador Senior',
                'Descripcion' => 'Desarrollo de software y sistemas',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Desarrollador Junior',
                'Descripcion' => 'Apoyo en desarrollo de software',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Analista de Sistemas',
                'Descripcion' => 'Análisis y diseño de sistemas informáticos',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Especialista en Seguridad',
                'Descripcion' => 'Seguridad informática y protección de datos',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            // Cargos administrativos
            [
                'Nombre' => 'Asistente Administrativo',
                'Descripcion' => 'Apoyo en tareas administrativas generales',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Contador',
                'Descripcion' => 'Gestión contable y financiera',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ],
            [
                'Nombre' => 'Especialista Legal',
                'Descripcion' => 'Asesoría jurídica y legal',
                'Estado' => true,
                'FechaCreacion' => now(),
                'UsuarioCreacion' => 'system'
            ]
        ];

        DB::table('cargos')->insert($cargos);
    }
}