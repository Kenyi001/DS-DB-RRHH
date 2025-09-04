<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empleados = [
            [
                'ci' => '7123456',
                'nombres' => 'Juan Carlos',
                'apellido_paterno' => 'González',
                'apellido_materno' => 'Pérez',
                'fecha_nacimiento' => '1985-03-15',
                'genero' => 'M',
                'estado_civil' => 'Casado',
                'telefono' => '2-2123456',
                'celular' => '70123456',
                'email' => 'juan.gonzalez@ypfb.gov.bo',
                'direccion' => 'Av. Arce #123, Zona San Jorge',
                'ciudad' => 'La Paz',
                'codigo_empleado' => 'YPFB001',
                'fecha_ingreso' => '2010-01-15',
                'estado' => 'Activo',
                'nacionalidad' => 'Boliviana',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ci' => '8234567',
                'nombres' => 'María Elena',
                'apellido_paterno' => 'Rodríguez',
                'apellido_materno' => 'López',
                'fecha_nacimiento' => '1990-07-22',
                'genero' => 'F',
                'estado_civil' => 'Soltero',
                'telefono' => '2-2234567',
                'celular' => '71234567',
                'email' => 'maria.rodriguez@ypfb.gov.bo',
                'direccion' => 'Calle Comercio #456, Zona Central',
                'ciudad' => 'Santa Cruz',
                'codigo_empleado' => 'YPFB002',
                'fecha_ingreso' => '2015-06-01',
                'estado' => 'Activo',
                'nacionalidad' => 'Boliviana',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ci' => '9345678',
                'nombres' => 'Carlos Alberto',
                'apellido_paterno' => 'Mendoza',
                'apellido_materno' => 'Silva',
                'fecha_nacimiento' => '1982-11-10',
                'genero' => 'M',
                'estado_civil' => 'Divorciado',
                'telefono' => null,
                'celular' => '72345678',
                'email' => 'carlos.mendoza@ypfb.gov.bo',
                'direccion' => 'Av. Heroínas #789, Zona Norte',
                'ciudad' => 'Cochabamba',
                'codigo_empleado' => 'YPFB003',
                'fecha_ingreso' => '2012-09-15',
                'estado' => 'Activo',
                'nacionalidad' => 'Boliviana',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ci' => '6456789',
                'nombres' => 'Ana Patricia',
                'apellido_paterno' => 'Vargas',
                'apellido_materno' => 'Morales',
                'fecha_nacimiento' => '1988-05-18',
                'genero' => 'F',
                'estado_civil' => 'Casado',
                'telefono' => '2-2345678',
                'celular' => '73456789',
                'email' => 'ana.vargas@ypfb.gov.bo',
                'direccion' => 'Calle Sucre #321, Zona Sur',
                'ciudad' => 'La Paz',
                'codigo_empleado' => 'YPFB004',
                'fecha_ingreso' => '2014-03-20',
                'estado' => 'Activo',
                'nacionalidad' => 'Boliviana',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ci' => '10567890',
                'nombres' => 'Luis Fernando',
                'apellido_paterno' => 'Torrez',
                'apellido_materno' => 'Gutiérrez',
                'fecha_nacimiento' => '1992-09-25',
                'genero' => 'M',
                'estado_civil' => 'Soltero',
                'telefono' => null,
                'celular' => '74567890',
                'email' => 'luis.torrez@ypfb.gov.bo',
                'direccion' => 'Av. Ballivián #654, Zona Miraflores',
                'ciudad' => 'La Paz',
                'codigo_empleado' => 'YPFB005',
                'fecha_ingreso' => '2020-01-10',
                'estado' => 'Activo',
                'nacionalidad' => 'Boliviana',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ci' => '11678901',
                'nombres' => 'Carmen Rosa',
                'apellido_paterno' => 'Flores',
                'apellido_materno' => 'Quispe',
                'fecha_nacimiento' => '1986-12-08',
                'genero' => 'F',
                'estado_civil' => 'Casado',
                'telefono' => '4-4678901',
                'celular' => '75678901',
                'email' => 'carmen.flores@ypfb.gov.bo',
                'direccion' => 'Calle España #987, Zona Este',
                'ciudad' => 'Santa Cruz',
                'codigo_empleado' => 'YPFB006',
                'fecha_ingreso' => '2016-08-15',
                'estado' => 'Vacaciones',
                'nacionalidad' => 'Boliviana',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ci' => '5789012',
                'nombres' => 'Roberto Miguel',
                'apellido_paterno' => 'Chávez',
                'apellido_materno' => 'Condori',
                'fecha_nacimiento' => '1979-04-12',
                'genero' => 'M',
                'estado_civil' => 'Viudo',
                'telefono' => '4-4789012',
                'celular' => '76789012',
                'email' => 'roberto.chavez@ypfb.gov.bo',
                'direccion' => 'Av. Cristo Redentor #147, Zona Villa 1ro de Mayo',
                'ciudad' => 'Cochabamba',
                'codigo_empleado' => 'YPFB007',
                'fecha_ingreso' => '2008-05-01',
                'estado' => 'Activo',
                'nacionalidad' => 'Boliviana',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ci' => '12890123',
                'nombres' => 'Paola Beatriz',
                'apellido_paterno' => 'Mamani',
                'apellido_materno' => null,
                'fecha_nacimiento' => '1994-08-30',
                'genero' => 'F',
                'estado_civil' => 'Soltero',
                'telefono' => null,
                'celular' => '77890123',
                'email' => 'paola.mamani@ypfb.gov.bo',
                'direccion' => 'Calle Potosí #258, Zona Villa Fatima',
                'ciudad' => 'La Paz',
                'codigo_empleado' => 'YPFB008',
                'fecha_ingreso' => '2022-02-15',
                'estado' => 'Activo',
                'nacionalidad' => 'Boliviana',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('empleados')->insert($empleados);
    }
}