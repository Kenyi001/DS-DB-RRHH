<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class EmpleadoMasivoSeeder extends Seeder
{
    private $nombresBolivianos = [
        'hombres' => [
            'Juan Carlos', 'Carlos Alberto', 'José Luis', 'Miguel Ángel', 'Roberto Carlos',
            'Luis Fernando', 'David Alexander', 'Gonzalo', 'Rodrigo', 'Sergio',
            'Fernando', 'Mario', 'Pedro', 'Antonio', 'Francisco',
            'Javier', 'Daniel', 'Eduardo', 'Andrés', 'Marcelo',
            'Oscar', 'Víctor Hugo', 'Ramón', 'Jorge', 'Alberto',
            'Rubén', 'Héctor', 'Pablo', 'Raúl', 'Edgar',
            'Wilson', 'Nelson', 'Freddy', 'Ronald', 'Walter',
            'Álvaro', 'Iván', 'Germán', 'Gustavo', 'Arturo',
            'Jaime', 'Mauricio', 'Ricardo', 'Leonardo', 'Fabricio',
            'Cristian', 'Rolando', 'Jhonny', 'Vladimir', 'Henry'
        ],
        'mujeres' => [
            'María Elena', 'Ana Lucía', 'Patricia', 'Sandra', 'Carla',
            'Mónica', 'Verónica', 'Claudia', 'Silvia', 'Rosa',
            'Carmen', 'Gloria', 'Susana', 'Adriana', 'Paola',
            'Cecilia', 'Graciela', 'Teresa', 'Beatriz', 'Norma',
            'Luz', 'Esperanza', 'Victoria', 'Elizabeth', 'Marlene',
            'Rosario', 'Isabel', 'Yolanda', 'Magda', 'Lourdes',
            'Marta', 'Nelly', 'Virginia', 'Delia', 'Miriam',
            'Alejandra', 'Karina', 'Lorena', 'Sonia', 'Rocío',
            'Maritza', 'Ximena', 'Vanesa', 'Jenny', 'Lidia',
            'Amparo', 'Soledad', 'Gladys', 'Viviana', 'Natalia'
        ]
    ];

    private $apellidosBolivianos = [
        'González', 'Rodríguez', 'Mendoza', 'Vargas', 'Choque', 'Morales',
        'Mamani', 'Quispe', 'Flores', 'López', 'García', 'Martínez',
        'Fernández', 'Pérez', 'Gómez', 'Sánchez', 'Ramírez', 'Torres',
        'Rivera', 'Hernández', 'Jiménez', 'Díaz', 'Álvarez', 'Romero',
        'Aguilar', 'Gutiérrez', 'Muñoz', 'Rojas', 'Castro', 'Ortega',
        'Ramos', 'Vásquez', 'Herrera', 'Medina', 'Silva', 'Contreras',
        'Espinoza', 'Guerrero', 'Cárdenas', 'Castillo', 'Mendez', 'Ruiz',
        'Paredes', 'Salinas', 'Montoya', 'Delgado', 'Campos', 'Carrasco',
        'Quiroga', 'Vera', 'Ibáñez', 'Molina', 'Condori', 'Apaza',
        'Huanca', 'Ticona', 'Callisaya', 'Yampara', 'Colque', 'Limachi'
    ];

    private $ciudadesBolivia = [
        'La Paz', 'Santa Cruz', 'Cochabamba', 'Oruro', 'Potosí',
        'Tarija', 'Sucre', 'Trinidad', 'Cobija', 'Riberalta',
        'Montero', 'Warnes', 'Quillacollo', 'Sacaba', 'Tiquipaya'
    ];

    public function run(): void
    {
        // Limpiar empleados existentes excepto los primeros 8 (datos base)
        DB::table('Empleados')->where('IDEmpleado', '>', 8)->delete();
        
        $faker = Faker::create('es_ES');
        $empleados = [];
        
        // Obtener IDs de departamentos y cargos existentes
        $departamentos = DB::table('Departamentos')->pluck('IDDepartamento')->toArray();
        $cargos = DB::table('Cargos')->pluck('IDCargo')->toArray();

        // Generar 300 empleados
        for ($i = 9; $i <= 308; $i++) {
            $esHombre = $faker->boolean(60); // 60% hombres, 40% mujeres
            
            $nombres = $esHombre 
                ? $faker->randomElement($this->nombresBolivianos['hombres'])
                : $faker->randomElement($this->nombresBolivianos['mujeres']);

            $apellidoPaterno = $faker->randomElement($this->apellidosBolivianos);
            $apellidoMaterno = $faker->randomElement($this->apellidosBolivianos);
            
            // Generar email corporativo
            $nombreEmail = strtolower(str_replace([' ', 'ñ', 'á', 'é', 'í', 'ó', 'ú'], 
                                                 ['', 'n', 'a', 'e', 'i', 'o', 'u'], 
                                                 explode(' ', $nombres)[0]));
            $apellidoEmail = strtolower(str_replace([' ', 'ñ', 'á', 'é', 'í', 'ó', 'ú'], 
                                                   ['', 'n', 'a', 'e', 'i', 'o', 'u'], 
                                                   $apellidoPaterno));
            
            $email = $nombreEmail . '.' . $apellidoEmail . '@ypfb.gov.bo';
            
            // Generar teléfono boliviano
            $telefono = '2-2' . $faker->numberBetween(100000, 999999);
            
            // Fecha de ingreso (entre 1 y 25 años atrás)
            $fechaIngreso = $faker->dateTimeBetween('-25 years', '-1 year')->format('Y-m-d');
            
            $empleados[] = [
                'Nombres' => $nombres,
                'ApellidoPaterno' => $apellidoPaterno,
                'ApellidoMaterno' => $apellidoMaterno,
                'FechaNacimiento' => $faker->dateTimeBetween('-65 years', '-22 years')->format('Y-m-d'),
                'Telefono' => $telefono,
                'Email' => $email,
                'Direccion' => $faker->address,
                'FechaIngreso' => $fechaIngreso,
                'Estado' => $faker->boolean(95) ? 1 : 0, // 95% activos
            ];
        }

        // Insertar en lotes de 50 para mejor rendimiento
        $chunks = array_chunk($empleados, 50);
        foreach ($chunks as $chunk) {
            DB::table('Empleados')->insert($chunk);
        }

        $this->command->info('✅ Generados 300 empleados masivos exitosamente');
    }
}