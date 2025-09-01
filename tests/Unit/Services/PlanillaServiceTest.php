<?php

namespace Tests\Unit\Services;

use App\Services\PlanillaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PlanillaServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlanillaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PlanillaService();
    }

    public function test_generar_planilla_exitosa(): void
    {
        // Arrange
        $mes = 8;
        $gestion = 2025;
        $usuario = 'test_user';

        // Mock de LogPlanilla para simular que no existe planilla previa
        DB::shouldReceive('table')
          ->with('LogPlanilla')
          ->andReturnSelf();
        
        DB::shouldReceive('where')
          ->with('Mes', $mes)
          ->andReturnSelf();
          
        DB::shouldReceive('where')
          ->with('Gestion', $gestion)
          ->andReturnSelf();
          
        DB::shouldReceive('where')
          ->with('EstadoProceso', 'Completado')
          ->andReturnSelf();
          
        DB::shouldReceive('first')
          ->andReturn(null);

        // Mock count de contratos activos
        DB::shouldReceive('table')
          ->with('contratos')
          ->andReturnSelf();
        
        DB::shouldReceive('where')
          ->with('Estado', 'Activo')
          ->andReturnSelf();
          
        DB::shouldReceive('whereRaw')
          ->andReturnSelf();
          
        DB::shouldReceive('count')
          ->andReturn(10);

        Log::shouldReceive('info')->twice();

        // Act
        $resultado = $this->service->generar($mes, $gestion, $usuario);

        // Assert
        $this->assertTrue($resultado['success']);
        $this->assertArrayHasKey('planilla_id', $resultado);
        $this->assertStringContains('8/2025', $resultado['message']);
    }

    public function test_generar_planilla_mes_invalido(): void
    {
        // Arrange
        $mes = 13; // Mes inválido
        $gestion = 2025;
        $usuario = 'test_user';

        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once();

        // Act
        $resultado = $this->service->generar($mes, $gestion, $usuario);

        // Assert
        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
        $this->assertStringContains('Mes debe estar entre 1 y 12', $resultado['error']);
    }

    public function test_generar_planilla_gestion_invalida(): void
    {
        // Arrange
        $mes = 8;
        $gestion = 2019; // Gestión muy antigua
        $usuario = 'test_user';

        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once();

        // Act
        $resultado = $this->service->generar($mes, $gestion, $usuario);

        // Assert
        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
        $this->assertStringContains('Gestión inválida', $resultado['error']);
    }

    public function test_planilla_ya_existente(): void
    {
        // Arrange
        $mes = 8;
        $gestion = 2025;
        $usuario = 'test_user';

        $planillaExistente = (object) [
            'PlanillaId' => 123,
            'EstadoProceso' => 'Completado'
        ];

        DB::shouldReceive('table')
          ->with('LogPlanilla')
          ->andReturnSelf();
        
        DB::shouldReceive('where')
          ->andReturnSelf();
          
        DB::shouldReceive('first')
          ->andReturn($planillaExistente);

        Log::shouldReceive('info')->once();

        // Act
        $resultado = $this->service->generar($mes, $gestion, $usuario);

        // Assert
        $this->assertTrue($resultado['success']);
        $this->assertEquals(123, $resultado['planilla_id']);
        $this->assertStringContains('ya existe', $resultado['message']);
    }

    public function test_get_estado_planilla_existente(): void
    {
        // Arrange
        $planillaId = 123;
        $estadoMock = (object) [
            'PlanillaId' => 123,
            'Mes' => 8,
            'Gestion' => 2025,
            'EstadoProceso' => 'Completado',
            'FechaInicio' => '2025-08-31 10:00:00',
            'FechaFin' => '2025-08-31 10:02:00',
            'ContratosProcessados' => 10
        ];

        DB::shouldReceive('table')
          ->with('LogPlanilla')
          ->andReturnSelf();
        
        DB::shouldReceive('where')
          ->with('PlanillaId', $planillaId)
          ->andReturnSelf();
          
        DB::shouldReceive('first')
          ->andReturn($estadoMock);

        // Act
        $estado = $this->service->getEstadoPlanilla($planillaId);

        // Assert
        $this->assertNotNull($estado);
        $this->assertEquals(123, $estado['planilla_id']);
        $this->assertEquals('Completado', $estado['estado']);
        $this->assertEquals(10, $estado['contratos_procesados']);
    }

    public function test_get_estado_planilla_inexistente(): void
    {
        // Arrange
        $planillaId = 999;

        DB::shouldReceive('table')
          ->with('LogPlanilla')
          ->andReturnSelf();
        
        DB::shouldReceive('where')
          ->with('PlanillaId', $planillaId)
          ->andReturnSelf();
          
        DB::shouldReceive('first')
          ->andReturn(null);

        // Act
        $estado = $this->service->getEstadoPlanilla($planillaId);

        // Assert
        $this->assertNull($estado);
    }
}