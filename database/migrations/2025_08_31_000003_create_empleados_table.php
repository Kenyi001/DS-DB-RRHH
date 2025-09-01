<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id('IDEmpleado');
            $table->string('CI', 15)->unique();
            $table->string('Nombres', 100);
            $table->string('ApellidoPaterno', 50);
            $table->string('ApellidoMaterno', 50)->nullable();
            $table->date('FechaNacimiento');
            $table->string('Email', 150)->unique();
            $table->string('Telefono', 20)->nullable();
            $table->boolean('Estado')->default(true);
            $table->timestamp('FechaCreacion')->useCurrent();
            $table->string('UsuarioCreacion', 100)->nullable();
            $table->timestamp('FechaModificacion')->nullable();
            $table->string('UsuarioModificacion', 100)->nullable();
            $table->timestamp('FechaBaja')->nullable();
            $table->string('UsuarioBaja', 100)->nullable();
            $table->binary('rowversion')->nullable();
            
            $table->index(['Estado']);
            $table->index(['CI']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};