<?php

namespace Tests\Unit;

use App\Modules\Empleados\Models\Empleado;
use Tests\TestCase;

class EmpleadoModelTest extends TestCase
{
    public function test_empleado_model_has_correct_fillable_fields()
    {
        $empleado = new Empleado();
        
        $expected = [
            'ci', 'nombres', 'apellido_paterno', 'apellido_materno',
            'fecha_nacimiento', 'genero', 'estado_civil', 'telefono',
            'celular', 'email', 'direccion', 'ciudad', 'codigo_empleado',
            'fecha_ingreso', 'estado', 'nacionalidad'
        ];
        
        $this->assertEquals($expected, $empleado->getFillable());
    }

    public function test_empleado_nombre_completo_accessor()
    {
        $empleado = new Empleado([
            'nombres' => 'Juan Carlos',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'López'
        ]);
        
        $this->assertEquals('Juan Carlos Pérez López', $empleado->nombre_completo);
    }

    public function test_empleado_has_correct_primary_key()
    {
        $empleado = new Empleado();
        
        $this->assertEquals('IDEmpleado', $empleado->getKeyName());
    }

    public function test_empleado_has_correct_table()
    {
        $empleado = new Empleado();
        
        $this->assertEquals('empleados', $empleado->getTable());
    }
}