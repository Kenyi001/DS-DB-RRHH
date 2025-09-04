<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Crear tabla Categorias según estructura SQL aprobada
        if (!Schema::hasTable('Categorias')) {
            Schema::create('Categorias', function (Blueprint $table) {
                $table->integer('IDCategoria')->primary()->autoIncrement();
                $table->char('NombreCategoria', 1);
                $table->string('DescripcionCategoria', 255)->nullable();
                $table->integer('AniosMinimos')->nullable();
                $table->integer('AniosMaximos')->nullable();
                $table->boolean('Estado')->default(1);
            });
        }

        // Eliminar tabla contratos antigua si existe
        if (Schema::hasTable('contratos')) {
            Schema::dropIfExists('contratos');
        }

        // Crear tabla Contratos según estructura SQL aprobada
        Schema::create('Contratos', function (Blueprint $table) {
            $table->integer('IDContrato')->primary()->autoIncrement();
            $table->integer('IDEmpleado');
            $table->integer('IDCategoria');
            $table->integer('IDCargo');
            $table->integer('IDDepartamento');
            $table->string('NumeroContrato', 50)->unique();
            $table->string('TipoContrato', 50)->default('Indefinido');
            $table->date('FechaContrato');
            $table->date('FechaInicio');
            $table->date('FechaFin')->nullable();
            $table->decimal('HaberBasico', 10, 2);
            $table->boolean('Estado')->default(1);

            // Foreign keys - usar mismo tipo que las tablas originales
            $table->foreign('IDEmpleado')->references('IDEmpleado')->on('Empleados');
            $table->foreign('IDCategoria')->references('IDCategoria')->on('Categorias');
            $table->foreign('IDCargo')->references('IDCargo')->on('Cargos');
            $table->foreign('IDDepartamento')->references('IDDepartamento')->on('Departamentos');
            
            // Índices
            $table->index(['IDEmpleado', 'Estado']);
            $table->index(['TipoContrato', 'Estado']);
            $table->index(['FechaInicio', 'FechaFin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Contratos');
        Schema::dropIfExists('Categorias');
    }
};