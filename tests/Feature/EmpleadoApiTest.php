<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Empleados\Models\Empleado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class EmpleadoApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear datos básicos para testing
        $this->createTestData();
    }

    private function createTestData()
    {
        // Crear departamento y cargo básicos
        $departamento = \App\Models\Departamento::create([
            'Nombre' => 'Tecnología',
            'Descripcion' => 'Departamento de Tecnología',
            'Estado' => true,
            'UsuarioCreacion' => 'TEST'
        ]);

        $cargo = \App\Models\Cargo::create([
            'Nombre' => 'Desarrollador',
            'Descripcion' => 'Desarrollador Senior',
            'Estado' => true,
            'UsuarioCreacion' => 'TEST'
        ]);

        // Crear empleados de prueba
        $empleado1 = \App\Modules\Empleados\Models\Empleado::create([
            'ci' => '12345678',
            'nombres' => 'Juan Carlos',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'López',
            'fecha_nacimiento' => '1990-01-15',
            'genero' => 'M',
            'estado_civil' => 'Soltero',
            'celular' => '71234567',
            'email' => 'juan.perez@test.com',
            'direccion' => 'Av. Test 123',
            'ciudad' => 'La Paz',
            'codigo_empleado' => 'EMP001',
            'fecha_ingreso' => '2020-01-01',
            'nacionalidad' => 'Boliviana',
            'departamento_id' => $departamento->IDDepartamento,
            'cargo_id' => $cargo->IDCargo,
            'estado' => 'Activo'
        ]);

        $empleado2 = \App\Modules\Empleados\Models\Empleado::create([
            'ci' => '87654321',
            'nombres' => 'María Elena',
            'apellido_paterno' => 'González',
            'apellido_materno' => 'Vargas',
            'fecha_nacimiento' => '1985-05-20',
            'genero' => 'F',
            'estado_civil' => 'Casada',
            'celular' => '71234568',
            'email' => 'maria.gonzalez@test.com',
            'direccion' => 'Calle Test 456',
            'ciudad' => 'Cochabamba',
            'codigo_empleado' => 'EMP002',
            'fecha_ingreso' => '2019-03-15',
            'nacionalidad' => 'Boliviana',
            'departamento_id' => $departamento->IDDepartamento,
            'cargo_id' => $cargo->IDCargo,
            'estado' => 'Activo'
        ]);

        // Crear usuarios de prueba
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ypfb.gov.bo',
            'password' => bcrypt('admin123'),
            'empleado_id' => $empleado1->IDEmpleado,
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@ypfb.gov.bo',
            'password' => bcrypt('user123'),
            'empleado_id' => $empleado2->IDEmpleado,
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_acceder_a_empleados()
    {
        $response = $this->getJson('/api/v1/empleados');
        
        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function usuario_autenticado_puede_listar_empleados()
    {
        $user = User::where('email', 'admin@ypfb.gov.bo')->first();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/empleados');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'empleados' => [
                             '*' => [
                                 'ci',
                                 'nombres',
                                 'apellido_paterno',
                                 'apellido_materno',
                                 'nombre_completo',
                                 'email',
                                 'codigo_empleado',
                                 'estado',
                             ]
                         ],
                         'resumen' => [
                             'total_registros',
                             'por_estado',
                             'por_genero'
                         ]
                     ],
                     'meta' => [
                         'current_page',
                         'per_page',
                         'total',
                         'last_page'
                     ]
                 ]);
    }

    /** @test */
    public function usuario_autenticado_puede_ver_empleado_especifico()
    {
        $user = User::where('email', 'admin@ypfb.gov.bo')->first();
        $empleado = Empleado::first();
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/empleados/{$empleado->IDEmpleado}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'ci',
                         'nombres',
                         'apellido_paterno',
                         'email',
                         'codigo_empleado'
                     ]
                 ]);
    }

    /** @test */
    public function admin_puede_crear_empleado()
    {
        $user = User::where('role', 'admin')->first();
        Sanctum::actingAs($user);

        $empleadoData = [
            'ci' => '1234567',
            'nombres' => 'Test User',
            'apellido_paterno' => 'Test',
            'apellido_materno' => 'Usuario',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'estado_civil' => 'Soltero',
            'celular' => '71234567',
            'email' => 'test.user@test.com',
            'direccion' => 'Test Address 123',
            'ciudad' => 'La Paz',
            'codigo_empleado' => 'TEST001',
            'fecha_ingreso' => '2025-01-01',
            'nacionalidad' => 'Boliviana'
        ];

        $response = $this->postJson('/api/v1/empleados', $empleadoData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Empleado creado exitosamente'
                 ]);

        $this->assertDatabaseHas('empleados', [
            'ci' => '1234567',
            'codigo_empleado' => 'TEST001'
        ]);
    }

    /** @test */
    public function usuario_regular_no_puede_crear_empleado()
    {
        $user = User::where('role', 'user')->first();
        Sanctum::actingAs($user);

        $empleadoData = [
            'ci' => '1234567',
            'nombres' => 'Test User',
            'apellido_paterno' => 'Test',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'estado_civil' => 'Soltero',
            'celular' => '71234567',
            'email' => 'test.user@test.com',
            'direccion' => 'Test Address 123',
            'ciudad' => 'La Paz',
            'codigo_empleado' => 'TEST001',
            'fecha_ingreso' => '2025-01-01',
            'nacionalidad' => 'Boliviana'
        ];

        $response = $this->postJson('/api/v1/empleados', $empleadoData);

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'No tienes permisos para acceder a este recurso'
                 ]);
    }

    /** @test */
    public function validacion_falla_con_datos_invalidos()
    {
        $user = User::where('role', 'admin')->first();
        Sanctum::actingAs($user);

        $empleadoData = [
            'ci' => '', // CI vacío
            'nombres' => '', // Nombres vacío
            'email' => 'invalid-email', // Email inválido
            'fecha_nacimiento' => '2030-01-01', // Fecha futura
        ];

        $response = $this->postJson('/api/v1/empleados', $empleadoData);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Errores de validación'
                 ])
                 ->assertJsonValidationErrors([
                     'ci',
                     'nombres',
                     'email',
                     'fecha_nacimiento'
                 ]);
    }

    /** @test */
    public function admin_puede_actualizar_empleado()
    {
        $user = User::where('role', 'admin')->first();
        $empleado = Empleado::first();
        Sanctum::actingAs($user);

        $updateData = [
            'nombres' => 'Nombre Actualizado',
            'apellido_paterno' => 'Apellido Actualizado'
        ];

        $response = $this->putJson("/api/v1/empleados/{$empleado->IDEmpleado}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Empleado actualizado exitosamente'
                 ]);

        $this->assertDatabaseHas('empleados', [
            'IDEmpleado' => $empleado->IDEmpleado,
            'nombres' => 'Nombre Actualizado',
            'apellido_paterno' => 'Apellido Actualizado'
        ]);
    }

    /** @test */
    public function admin_puede_eliminar_empleado()
    {
        $user = User::where('role', 'admin')->first();
        $empleado = Empleado::first();
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/empleados/{$empleado->IDEmpleado}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Empleado eliminado exitosamente'
                 ]);

        // Verificar que el empleado fue marcado como inactivo (soft delete)
        $this->assertDatabaseHas('empleados', [
            'IDEmpleado' => $empleado->IDEmpleado,
            'estado' => 'Inactivo'
        ]);
    }

    /** @test */
    public function filtros_funcionan_correctamente()
    {
        $user = User::where('role', 'admin')->first();
        Sanctum::actingAs($user);

        // Test filtro por género
        $response = $this->getJson('/api/v1/empleados?genero=M');
        $response->assertStatus(200);
        
        $empleados = $response->json('data.empleados');
        foreach ($empleados as $empleado) {
            $this->assertEquals('M', $empleado['genero']);
        }

        // Test filtro por estado
        $response = $this->getJson('/api/v1/empleados?estado=Activo');
        $response->assertStatus(200);
        
        $empleados = $response->json('data.empleados');
        foreach ($empleados as $empleado) {
            $this->assertEquals('Activo', $empleado['estado']);
        }
    }

    /** @test */
    public function busqueda_por_nombre_funciona()
    {
        $user = User::where('role', 'admin')->first();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/empleados?search=Juan');

        $response->assertStatus(200);
        
        $empleados = $response->json('data.empleados');
        $this->assertGreaterThan(0, count($empleados));
        
        // Verificar que al menos un empleado contiene "Juan" en su nombre
        $foundMatch = false;
        foreach ($empleados as $empleado) {
            if (stripos($empleado['nombres'], 'Juan') !== false || 
                stripos($empleado['apellido_paterno'], 'Juan') !== false) {
                $foundMatch = true;
                break;
            }
        }
        $this->assertTrue($foundMatch);
    }

    /** @test */
    public function paginacion_funciona_correctamente()
    {
        $user = User::where('role', 'admin')->first();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/empleados?per_page=3&page=1');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'meta' => [
                         'current_page',
                         'per_page',
                         'total',
                         'last_page'
                     ]
                 ]);

        $meta = $response->json('meta');
        $this->assertEquals(3, $meta['per_page']);
        $this->assertEquals(1, $meta['current_page']);
    }

    /** @test */
    public function empleado_inexistente_devuelve_404()
    {
        $user = User::where('role', 'admin')->first();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/empleados/99999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Empleado no encontrado'
                 ]);
    }
}