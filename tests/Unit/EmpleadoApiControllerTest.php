<?php

namespace Tests\Unit;

use App\Modules\Empleados\Controllers\Api\EmpleadoApiController;
use App\Modules\Empleados\Services\EmpleadoService;
use Illuminate\Http\Request;
use Tests\TestCase;
use Mockery;

class EmpleadoApiControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(EmpleadoService::class);
        $controller = new EmpleadoApiController($mockService);
        
        $this->assertInstanceOf(EmpleadoApiController::class, $controller);
    }

    public function test_controller_has_required_methods()
    {
        $mockService = Mockery::mock(EmpleadoService::class);
        $controller = new EmpleadoApiController($mockService);
        
        $this->assertTrue(method_exists($controller, 'index'));
        $this->assertTrue(method_exists($controller, 'show'));
        $this->assertTrue(method_exists($controller, 'store'));
        $this->assertTrue(method_exists($controller, 'update'));
        $this->assertTrue(method_exists($controller, 'destroy'));
    }
}