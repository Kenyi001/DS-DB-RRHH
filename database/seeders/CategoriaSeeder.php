<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tabla (sin truncate por foreign keys)
        DB::table('Categorias')->delete();

        $categorias = [
            [
                'NombreCategoria' => 'A',
                'DescripcionCategoria' => 'Personal Gerencial - Nivel ejecutivo y directivo',
                'AniosMinimos' => 5,
                'AniosMaximos' => null,
                'Estado' => 1
            ],
            [
                'NombreCategoria' => 'B',
                'DescripcionCategoria' => 'Personal de Jefatura - Nivel intermedio de supervisión',
                'AniosMinimos' => 3,
                'AniosMaximos' => 15,
                'Estado' => 1
            ],
            [
                'NombreCategoria' => 'C',
                'DescripcionCategoria' => 'Personal Profesional - Nivel técnico y profesional',
                'AniosMinimos' => 0,
                'AniosMaximos' => 10,
                'Estado' => 1
            ],
            [
                'NombreCategoria' => 'D',
                'DescripcionCategoria' => 'Personal Técnico - Nivel técnico especializado',
                'AniosMinimos' => 0,
                'AniosMaximos' => 8,
                'Estado' => 1
            ],
            [
                'NombreCategoria' => 'E',
                'DescripcionCategoria' => 'Personal Administrativo - Nivel de apoyo administrativo',
                'AniosMinimos' => 0,
                'AniosMaximos' => 5,
                'Estado' => 1
            ],
            [
                'NombreCategoria' => 'F',
                'DescripcionCategoria' => 'Personal de Práctica - Estudiantes en formación',
                'AniosMinimos' => 0,
                'AniosMaximos' => 1,
                'Estado' => 1
            ]
        ];

        // Insertar con autoincrement
        foreach ($categorias as $categoria) {
            DB::table('Categorias')->insert($categoria);
        }
    }
}