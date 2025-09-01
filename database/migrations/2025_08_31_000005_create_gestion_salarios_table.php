<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestion_salarios', function (Blueprint $table) {
            $table->id('IDGestion');
            $table->foreignId('IDContrato')->constrained('contratos', 'IDContrato');
            $table->integer('Mes');
            $table->integer('Gestion');
            $table->decimal('HaberBasico', 10, 2);
            $table->decimal('TotalSubsidios', 10, 2)->default(0);
            $table->decimal('TotalDescuentos', 10, 2)->default(0);
            $table->decimal('LiquidoPagable', 10, 2);
            $table->date('FechaPago')->nullable();
            $table->string('Estado', 20)->default('Generado');
            $table->timestamp('FechaCreacion')->useCurrent();
            $table->string('UsuarioCreacion', 100)->nullable();
            $table->timestamp('FechaModificacion')->nullable();
            $table->string('UsuarioModificacion', 100)->nullable();
            
            $table->unique(['IDContrato', 'Mes', 'Gestion']);
            $table->index(['Gestion', 'Mes']);
            $table->index(['Estado', 'FechaPago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gestion_salarios');
    }
};