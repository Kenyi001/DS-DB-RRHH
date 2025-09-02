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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Relación con empleado - usar integer para coincidir con tipo de IDEmpleado
            $table->integer('empleado_id')->nullable();
            $table->foreign('empleado_id')->references('IDEmpleado')->on('Empleados');
            
            // Roles y permisos
            $table->enum('role', ['admin', 'manager', 'user'])->default('user');
            $table->boolean('is_active')->default(true);
            
            // Campos adicionales del usuario
            $table->string('avatar_path')->nullable();
            $table->string('phone')->nullable();
            
            // Seguridad y auditoría
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            
            // Preferencias del usuario
            $table->json('preferences')->nullable();
            $table->string('theme')->default('light');
            $table->string('language')->default('es');
            $table->string('timezone')->default('America/La_Paz');
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamp('password_changed_at')->nullable();
            $table->string('status')->default('active');
            
            $table->rememberToken();
            $table->timestamps();
            
            // Índices
            $table->index(['role', 'is_active']);
            $table->index('empleado_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};