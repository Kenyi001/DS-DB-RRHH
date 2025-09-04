<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario admin de prueba
        User::updateOrCreate(
            ['email' => 'admin@ypfb.gov.bo'],
            [
                'name' => 'Administrador YPFB',
                'email' => 'admin@ypfb.gov.bo',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'empleado_id' => 1,
                'email_verified_at' => now(),
            ]
        );

        // Crear usuario manager de prueba
        User::updateOrCreate(
            ['email' => 'manager@ypfb.gov.bo'],
            [
                'name' => 'Gerente RRHH',
                'email' => 'manager@ypfb.gov.bo',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'is_active' => true,
                'empleado_id' => 2,
                'email_verified_at' => now(),
            ]
        );

        // Crear usuario normal de prueba
        User::updateOrCreate(
            ['email' => 'user@ypfb.gov.bo'],
            [
                'name' => 'Usuario Normal',
                'email' => 'user@ypfb.gov.bo',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'is_active' => true,
                'empleado_id' => 3,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Usuarios de prueba creados exitosamente:');
        $this->command->info('   Admin: admin@ypfb.gov.bo / admin123');
        $this->command->info('   Manager: manager@ypfb.gov.bo / manager123');
        $this->command->info('   User: user@ypfb.gov.bo / user123');
    }
}