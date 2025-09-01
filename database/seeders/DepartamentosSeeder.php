<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentosSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = [
            ['Nombre' => 'Recursos Humanos', 'Descripcion' => 'Gestión de personal y nómina', 'Estado' => true],
            ['Nombre' => 'Contabilidad', 'Descripcion' => 'Gestión financiera y contable', 'Estado' => true],
            ['Nombre' => 'Operaciones', 'Descripcion' => 'Operaciones de exploración y producción', 'Estado' => true],
            ['Nombre' => 'Tecnología', 'Descripcion' => 'Sistemas e infraestructura tecnológica', 'Estado' => true],
            ['Nombre' => 'Legal', 'Descripcion' => 'Asuntos legales y compliance', 'Estado' => true],
            ['Nombre' => 'Comercial', 'Descripcion' => 'Ventas y relaciones comerciales', 'Estado' => true],
            ['Nombre' => 'Seguridad Industrial', 'Descripcion' => 'Seguridad y medio ambiente', 'Estado' => true],
            ['Nombre' => 'Gerencia General', 'Descripcion' => 'Dirección ejecutiva', 'Estado' => true]
        ];

        foreach ($departamentos as $dept) {
            DB::table('departamentos')->insert([
                'Nombre' => $dept['Nombre'],
                'Descripcion' => $dept['Descripcion'],
                'Estado' => $dept['Estado'],
                'UsuarioCreacion' => 'SEEDER',
                'FechaCreacion' => now()
            ]);
        }
    }
}