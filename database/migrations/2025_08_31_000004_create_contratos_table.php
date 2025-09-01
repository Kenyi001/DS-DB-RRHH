<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id('IDContrato');
            $table->foreignId('IDEmpleado')->constrained('empleados', 'IDEmpleado');
            $table->foreignId('IDDepartamento')->constrained('departamentos', 'IDDepartamento');
            $table->foreignId('IDCargo')->constrained('cargos', 'IDCargo');
            $table->string('NumeroContrato', 50)->unique();
            $table->date('FechaInicio');
            $table->date('FechaFin')->nullable();
            $table->decimal('HaberBasico', 10, 2);
            $table->string('Estado', 20)->default('Activo');
            $table->text('Observaciones')->nullable();
            $table->timestamp('FechaCreacion')->useCurrent();
            $table->string('UsuarioCreacion', 100)->nullable();
            $table->timestamp('FechaModificacion')->nullable();
            $table->string('UsuarioModificacion', 100)->nullable();
            $table->binary('rowversion')->nullable();
            
            $table->index(['IDEmpleado', 'Estado']);
            $table->index(['IDDepartamento', 'IDCargo', 'Estado']);
            $table->index(['FechaInicio', 'FechaFin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};