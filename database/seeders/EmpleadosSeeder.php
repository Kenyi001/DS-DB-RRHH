<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmpleadosSeeder extends Seeder
{
    public function run(): void
    {
        $empleados = [
            [
                'CI' => '12345678',
                'Nombres' => 'Juan Carlos',
                'ApellidoPaterno' => 'Pérez',
                'ApellidoMaterno' => 'González',
                'FechaNacimiento' => '1985-03-15',
                'Email' => 'juan.perez@ypfb.gov.bo',
                'Telefono' => '591-70123456'
            ],
            [
                'CI' => '87654321',
                'Nombres' => 'María Elena',
                'ApellidoPaterno' => 'Rodríguez',
                'ApellidoMaterno' => 'Vargas',
                'FechaNacimiento' => '1990-07-22',
                'Email' => 'maria.rodriguez@ypfb.gov.bo',
                'Telefono' => '591-71234567'
            ],
            [
                'CI' => '11223344',
                'Nombres' => 'Carlos Alberto',
                'ApellidoPaterno' => 'Mamani',
                'ApellidoMaterno' => 'Quispe',
                'FechaNacimiento' => '1982-11-08',
                'Email' => 'carlos.mamani@ypfb.gov.bo',
                'Telefono' => '591-72345678'
            ],
            [
                'CI' => '55667788',
                'Nombres' => 'Ana Lucia',
                'ApellidoPaterno' => 'Fernández',
                'ApellidoMaterno' => 'Morales',
                'FechaNacimiento' => '1988-04-12',
                'Email' => 'ana.fernandez@ypfb.gov.bo',
                'Telefono' => '591-73456789'
            ],
            [
                'CI' => '99887766',
                'Nombres' => 'Roberto',
                'ApellidoPaterno' => 'Choque',
                'ApellidoMaterno' => 'Condori',
                'FechaNacimiento' => '1975-09-25',
                'Email' => 'roberto.choque@ypfb.gov.bo',
                'Telefono' => '591-74567890'
            ],
            [
                'CI' => '66778899',
                'Nombres' => 'Patricia Isabel',
                'ApellidoPaterno' => 'Vega',
                'ApellidoMaterno' => 'Salinas',
                'FechaNacimiento' => '1993-01-17',
                'Email' => 'patricia.vega@ypfb.gov.bo',
                'Telefono' => '591-75678901'
            ],
            [
                'CI' => '33445566',
                'Nombres' => 'Miguel Angel',
                'ApellidoPaterno' => 'Torrez',
                'ApellidoMaterno' => 'Jiménez',
                'FechaNacimiento' => '1987-06-30',
                'Email' => 'miguel.torrez@ypfb.gov.bo',
                'Telefono' => '591-76789012'
            ],
            [
                'CI' => '77889900',
                'Nombres' => 'Silvia Beatriz',
                'ApellidoPaterno' => 'Campos',
                'ApellidoMaterno' => 'Rivero',
                'FechaNacimiento' => '1991-12-03',
                'Email' => 'silvia.campos@ypfb.gov.bo',
                'Telefono' => '591-77890123'
            ],
            [
                'CI' => '44556677',
                'Nombres' => 'Luis Fernando',
                'ApellidoPaterno' => 'Gutiérrez',
                'ApellidoMaterno' => 'Herrera',
                'FechaNacimiento' => '1984-08-14',
                'Email' => 'luis.gutierrez@ypfb.gov.bo',
                'Telefono' => '591-78901234'
            ],
            [
                'CI' => '22334455',
                'Nombres' => 'Carmen Rosa',
                'ApellidoPaterno' => 'Mendoza',
                'ApellidoMaterno' => 'Flores',
                'FechaNacimiento' => '1989-05-28',
                'Email' => 'carmen.mendoza@ypfb.gov.bo',
                'Telefono' => '591-79012345'
            ]
        ];

        foreach ($empleados as $emp) {
            DB::table('empleados')->insert([
                'CI' => $emp['CI'],
                'Nombres' => $emp['Nombres'],
                'ApellidoPaterno' => $emp['ApellidoPaterno'],
                'ApellidoMaterno' => $emp['ApellidoMaterno'],
                'FechaNacimiento' => $emp['FechaNacimiento'],
                'Email' => $emp['Email'],
                'Telefono' => $emp['Telefono'],
                'Estado' => true,
                'UsuarioCreacion' => 'SEEDER',
                'FechaCreacion' => now()
            ]);
        }
    }
}