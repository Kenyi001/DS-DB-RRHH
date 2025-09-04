<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id('IDEmpleado');
            
            // Información personal
            $table->string('ci', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellido_paterno', 50);
            $table->string('apellido_materno', 50)->nullable();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['M', 'F']);
            $table->enum('estado_civil', ['Soltero', 'Casado', 'Divorciado', 'Viudo']);
            
            // Información de contacto
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 150)->unique();
            $table->text('direccion');
            $table->string('ciudad', 50);
            
            // Información laboral
            $table->string('codigo_empleado', 20)->unique();
            $table->date('fecha_ingreso');
            $table->enum('estado', ['Activo', 'Inactivo', 'Vacaciones', 'Licencia'])->default('Activo');
            $table->string('nacionalidad', 50)->default('Boliviana');
            
            // Soft delete y auditoría
            $table->timestamp('fecha_baja')->nullable();
            $table->string('usuario_baja', 50)->nullable();
            $table->string('motivo_baja', 255)->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Índices
            $table->index(['estado', 'fecha_ingreso']);
            $table->index(['apellido_paterno', 'nombres']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
