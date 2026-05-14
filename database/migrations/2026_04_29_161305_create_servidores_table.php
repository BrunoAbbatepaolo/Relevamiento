<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servidores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relevamiento_id')->constrained('relevamientos')->cascadeOnDelete();

            // Datos generales
            $table->boolean('tiene_servidor')->default(false);
            $table->string('funcion')->nullable();
            $table->string('sistema_operativo')->nullable();
            $table->string('so_version')->nullable();
            $table->string('arquitectura')->nullable();
            $table->text('requerimientos_especiales')->nullable();

            // Hardware
            $table->string('tipo_equipamiento')->nullable();
            $table->decimal('cpu_ghz', 5, 2)->nullable();
            $table->integer('ram_gb')->nullable();
            $table->integer('swap_gb')->nullable();
            $table->integer('almacenamiento_gb')->nullable();

            // Base de datos
            $table->string('motor_bd')->nullable();
            $table->integer('cantidad_bases')->nullable();
            $table->integer('tamano_bases_gb')->nullable();

            // Usuarios
            $table->integer('usuarios_concurrentes')->nullable();
            $table->integer('usuarios_totales')->nullable();

            // Crecimiento
            $table->text('estimacion_crecimiento')->nullable();

            // Licencias
            $table->string('licencia_tipo')->nullable();
            $table->integer('licencia_cantidad')->nullable();
            $table->text('licencia_observaciones')->nullable();

            // Red
            $table->string('red_lan')->nullable();
            $table->string('internet_dedicado')->nullable();
            $table->string('wan')->nullable();
            $table->integer('trafico_estimado_mb')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servidores');
    }
};
