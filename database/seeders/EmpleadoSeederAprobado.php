<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpleadoSeederAprobado extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empleados = [
            [
                'Nombres' => 'Juan Carlos',
                'ApellidoPaterno' => 'González',
                'ApellidoMaterno' => 'Pérez',
                'FechaNacimiento' => '1985-03-15',
                'Telefono' => '2-2123456',
                'Email' => 'juan.gonzalez@ypfb.gov.bo',
                'Direccion' => 'Av. Arce #123, Zona San Jorge',
                'FechaIngreso' => '2020-01-15',
                'Estado' => 1
            ],
            [
                'Nombres' => 'María Elena',
                'ApellidoPaterno' => 'Rodríguez',
                'ApellidoMaterno' => 'Mamani',
                'FechaNacimiento' => '1990-07-22',
                'Telefono' => '2-2234567',
                'Email' => 'maria.rodriguez@ypfb.gov.bo',
                'Direccion' => 'Calle Murillo #456, Zona Centro',
                'FechaIngreso' => '2021-03-10',
                'Estado' => 1
            ],
            [
                'Nombres' => 'Carlos Alberto',
                'ApellidoPaterno' => 'Mendoza',
                'ApellidoMaterno' => 'Quispe',
                'FechaNacimiento' => '1988-11-08',
                'Telefono' => '2-2345678',
                'Email' => 'carlos.mendoza@ypfb.gov.bo',
                'Direccion' => 'Av. 6 de Agosto #789, Zona San Miguel',
                'FechaIngreso' => '2019-06-01',
                'Estado' => 1
            ],
            [
                'Nombres' => 'Ana Lucía',
                'ApellidoPaterno' => 'Vargas',
                'ApellidoMaterno' => 'Flores',
                'FechaNacimiento' => '1992-04-18',
                'Telefono' => '2-2456789',
                'Email' => 'ana.vargas@ypfb.gov.bo',
                'Direccion' => 'Calle 21 de Calacoto #321, Zona Sur',
                'FechaIngreso' => '2022-01-20',
                'Estado' => 1
            ],
            [
                'Nombres' => 'Roberto',
                'ApellidoPaterno' => 'Choque',
                'ApellidoMaterno' => 'Mamani',
                'FechaNacimiento' => '1987-09-12',
                'Telefono' => '2-2567890',
                'Email' => 'roberto.choque@ypfb.gov.bo',
                'Direccion' => 'Av. Ballivián #654, Zona Sur',
                'FechaIngreso' => '2018-09-05',
                'Estado' => 1
            ],
            [
                'Nombres' => 'Patricia',
                'ApellidoPaterno' => 'Morales',
                'ApellidoMaterno' => 'Condori',
                'FechaNacimiento' => '1991-12-03',
                'Telefono' => '2-2678901',
                'Email' => 'patricia.morales@ypfb.gov.bo',
                'Direccion' => 'Calle Rosendo Gutiérrez #987, Zona Centro',
                'FechaIngreso' => '2021-07-15',
                'Estado' => 1
            ],
            [
                'Nombres' => 'Miguel Ángel',
                'ApellidoPaterno' => 'Huanca',
                'ApellidoMaterno' => 'Ticona',
                'FechaNacimiento' => '1986-02-25',
                'Telefono' => '2-2789012',
                'Email' => 'miguel.huanca@ypfb.gov.bo',
                'Direccion' => 'Av. América #147, Zona Miraflores',
                'FechaIngreso' => '2017-11-10',
                'Estado' => 1
            ],
            [
                'Nombres' => 'Sandra',
                'ApellidoPaterno' => 'Apaza',
                'ApellidoMaterno' => 'Villarroel',
                'FechaNacimiento' => '1989-06-14',
                'Telefono' => '2-2890123',
                'Email' => 'sandra.apaza@ypfb.gov.bo',
                'Direccion' => 'Calle Potosí #258, Zona Central',
                'FechaIngreso' => '2020-04-08',
                'Estado' => 1
            ]
        ];

        DB::table('Empleados')->insert($empleados);
    }
}