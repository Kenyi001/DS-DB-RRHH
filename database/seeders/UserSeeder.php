<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin principal
        User::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@ypfb.gov.bo',
            'password' => Hash::make('admin123'),
            'empleado_id' => 7, // Juan Carlos González (primer empleado)
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Manager de RRHH
        User::create([
            'name' => 'María Elena Rodríguez',
            'email' => 'maria.rodriguez@ypfb.gov.bo',
            'password' => Hash::make('maria123'),
            'empleado_id' => 8, // María Elena
            'role' => 'manager',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario regular - Carlos Mendoza
        User::create([
            'name' => 'Carlos Alberto Mendoza',
            'email' => 'carlos.mendoza@ypfb.gov.bo',
            'password' => Hash::make('carlos123'),
            'empleado_id' => 9, // Carlos Alberto
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario regular - Ana Vargas
        User::create([
            'name' => 'Ana Patricia Vargas',
            'email' => 'ana.vargas@ypfb.gov.bo',
            'password' => Hash::make('ana123'),
            'empleado_id' => 10, // Ana Patricia
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario regular - Luis Torrez  
        User::create([
            'name' => 'Luis Fernando Torrez',
            'email' => 'luis.torrez@ypfb.gov.bo',
            'password' => Hash::make('luis123'),
            'empleado_id' => 11, // Luis Fernando
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario inactivo para pruebas
        User::create([
            'name' => 'Usuario Inactivo',
            'email' => 'inactivo@ypfb.gov.bo',
            'password' => Hash::make('inactivo123'),
            'empleado_id' => null,
            'role' => 'user',
            'is_active' => false,
            'email_verified_at' => now(),
        ]);
    }
}