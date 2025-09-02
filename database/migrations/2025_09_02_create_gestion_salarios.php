<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('GestionSalarios', function (Blueprint $table) {
            $table->integer('IDGestionSalario')->primary()->autoIncrement();
            $table->integer('IDContrato');
            $table->integer('Mes')->check('Mes BETWEEN 1 AND 12');
            $table->integer('Gestion');
            $table->integer('DiasTrabajos')->default(30);
            $table->decimal('SalarioBasico', 10, 2);
            $table->decimal('TotalIngresos', 10, 2);
            $table->decimal('TotalDescuentos', 10, 2);
            $table->decimal('LiquidoPagable', 10, 2);
            $table->date('FechaPago')->nullable();
            $table->enum('EstadoPago', ['Pendiente', 'Pagado', 'Anulado'])->default('Pendiente');

            // Foreign keys
            $table->foreign('IDContrato')->references('IDContrato')->on('Contratos');
            
            // Índices y constraints
            $table->unique(['IDContrato', 'Mes', 'Gestion'], 'unique_contrato_periodo');
            $table->index(['Gestion', 'Mes']);
            $table->index(['EstadoPago']);
            $table->index(['FechaPago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('GestionSalarios');
    }
};