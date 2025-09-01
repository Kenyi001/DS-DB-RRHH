<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id('IDDepartamento');
            $table->string('Nombre', 100);
            $table->text('Descripcion')->nullable();
            $table->boolean('Estado')->default(true);
            $table->timestamp('FechaCreacion')->useCurrent();
            $table->string('UsuarioCreacion', 100)->nullable();
            $table->timestamp('FechaModificacion')->nullable();
            $table->string('UsuarioModificacion', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};