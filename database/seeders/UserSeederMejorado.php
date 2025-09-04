<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeederMejorado extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eliminar usuarios existentes
        DB::table('users')->truncate();

        $usuarios = [
            [
                'name' => 'Administrador del Sistema',
                'email' => 'admin@ypfb.gov.bo',
                'password' => Hash::make('admin123'),
                'empleado_id' => 1, // Juan Carlos González
                'role' => 'admin',
                'is_active' => 1,
                'email_verified_at' => now(),
                'avatar_path' => null,
                'phone' => '70123456',
                'last_login_at' => now()->subHours(2),
                'last_login_ip' => '192.168.1.10',
                'login_attempts' => 0,
                'locked_until' => null,
                'preferences' => json_encode([
                    'dashboard' => [
                        'widgets' => ['empleados', 'reportes', 'notificaciones'],
                        'layout' => 'grid'
                    ],
                    'notifications' => [
                        'email' => true,
                        'browser' => true,
                        'reports' => true
                    ]
                ]),
                'theme' => 'light',
                'language' => 'es',
                'timezone' => 'America/La_Paz',
                'notifications_enabled' => 1,
                'two_factor_enabled' => 0,
                'password_changed_at' => now()->subMonths(2),
                'status' => 'active',
                'created_at' => now()->subMonths(6),
                'updated_at' => now()->subHours(2)
            ],
            [
                'name' => 'María Elena Rodríguez',
                'email' => 'maria.rodriguez@ypfb.gov.bo',
                'password' => Hash::make('maria123'),
                'empleado_id' => 2, // María Elena Rodríguez
                'role' => 'manager',
                'is_active' => 1,
                'email_verified_at' => now(),
                'avatar_path' => null,
                'phone' => '70234567',
                'last_login_at' => now()->subMinutes(30),
                'last_login_ip' => '192.168.1.25',
                'login_attempts' => 0,
                'locked_until' => null,
                'preferences' => json_encode([
                    'dashboard' => [
                        'widgets' => ['empleados', 'contratos'],
                        'layout' => 'list'
                    ],
                    'notifications' => [
                        'email' => true,
                        'browser' => false,
                        'reports' => true
                    ]
                ]),
                'theme' => 'light',
                'language' => 'es',
                'timezone' => 'America/La_Paz',
                'notifications_enabled' => 1,
                'two_factor_enabled' => 0,
                'password_changed_at' => now()->subMonths(1),
                'status' => 'active',
                'created_at' => now()->subMonths(4),
                'updated_at' => now()->subMinutes(30)
            ],
            [
                'name' => 'Carlos Alberto Mendoza',
                'email' => 'carlos.mendoza@ypfb.gov.bo',
                'password' => Hash::make('carlos123'),
                'empleado_id' => 3, // Carlos Alberto Mendoza
                'role' => 'user',
                'is_active' => 1,
                'email_verified_at' => now(),
                'avatar_path' => null,
                'phone' => '70345678',
                'last_login_at' => now()->subDays(1),
                'last_login_ip' => '192.168.1.15',
                'login_attempts' => 0,
                'locked_until' => null,
                'preferences' => json_encode([
                    'dashboard' => [
                        'widgets' => ['mi_perfil'],
                        'layout' => 'simple'
                    ],
                    'notifications' => [
                        'email' => false,
                        'browser' => true,
                        'reports' => false
                    ]
                ]),
                'theme' => 'dark',
                'language' => 'es',
                'timezone' => 'America/La_Paz',
                'notifications_enabled' => 0,
                'two_factor_enabled' => 0,
                'password_changed_at' => now()->subWeeks(3),
                'status' => 'active',
                'created_at' => now()->subMonths(3),
                'updated_at' => now()->subDays(1)
            ],
            [
                'name' => 'Ana Lucía Vargas',
                'email' => 'ana.vargas@ypfb.gov.bo',
                'password' => Hash::make('ana123'),
                'empleado_id' => 4, // Ana Lucía Vargas
                'role' => 'manager',
                'is_active' => 1,
                'email_verified_at' => now(),
                'avatar_path' => null,
                'phone' => '70456789',
                'last_login_at' => now()->subHours(8),
                'last_login_ip' => '192.168.1.45',
                'login_attempts' => 0,
                'locked_until' => null,
                'preferences' => json_encode([
                    'dashboard' => [
                        'widgets' => ['empleados', 'evaluaciones'],
                        'layout' => 'grid'
                    ],
                    'notifications' => [
                        'email' => true,
                        'browser' => true,
                        'reports' => false
                    ]
                ]),
                'theme' => 'light',
                'language' => 'es',
                'timezone' => 'America/La_Paz',
                'notifications_enabled' => 1,
                'two_factor_enabled' => 1, // Tiene 2FA habilitado
                'password_changed_at' => now()->subWeeks(2),
                'status' => 'active',
                'created_at' => now()->subMonths(2),
                'updated_at' => now()->subHours(8)
            ],
            [
                'name' => 'Roberto Choque',
                'email' => 'roberto.choque@ypfb.gov.bo',
                'password' => Hash::make('roberto123'),
                'empleado_id' => 5, // Roberto Choque
                'role' => 'user',
                'is_active' => 0, // Usuario inactivo para pruebas
                'email_verified_at' => now(),
                'avatar_path' => null,
                'phone' => '70567890',
                'last_login_at' => now()->subWeeks(2),
                'last_login_ip' => '192.168.1.55',
                'login_attempts' => 3, // Algunos intentos fallidos
                'locked_until' => null,
                'preferences' => json_encode([
                    'dashboard' => [
                        'widgets' => ['mi_perfil'],
                        'layout' => 'simple'
                    ]
                ]),
                'theme' => 'light',
                'language' => 'es',
                'timezone' => 'America/La_Paz',
                'notifications_enabled' => 1,
                'two_factor_enabled' => 0,
                'password_changed_at' => now()->subMonths(4),
                'status' => 'inactive',
                'created_at' => now()->subMonths(5),
                'updated_at' => now()->subWeeks(2)
            ],
            [
                'name' => 'Patricia Morales',
                'email' => 'patricia.morales@ypfb.gov.bo',
                'password' => Hash::make('patricia123'),
                'empleado_id' => 6, // Patricia Morales
                'role' => 'user',
                'is_active' => 1,
                'email_verified_at' => null, // Email no verificado para pruebas
                'avatar_path' => null,
                'phone' => '70678901',
                'last_login_at' => null, // Nunca se ha logueado
                'last_login_ip' => null,
                'login_attempts' => 0,
                'locked_until' => null,
                'preferences' => null, // Sin preferencias configuradas
                'theme' => 'light',
                'language' => 'es',
                'timezone' => 'America/La_Paz',
                'notifications_enabled' => 1,
                'two_factor_enabled' => 0,
                'password_changed_at' => null,
                'status' => 'pending_verification',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7)
            ]
        ];

        DB::table('users')->insert($usuarios);
    }
}