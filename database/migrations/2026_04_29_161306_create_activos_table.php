<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relevamiento_id')->constrained('relevamientos')->cascadeOnDelete();

            // Identificación
            $table->string('codigo_inventario')->nullable();
            $table->enum('tipo', ['desktop', 'notebook', 'thin_client']);
            $table->enum('estado', ['activo', 'inactivo', 'reparacion'])->default('activo');

            // Red
            $table->enum('tipo_red', ['ip_fija', 'dhcp', 'wifi'])->nullable();
            $table->string('ip')->nullable();

            // Sistema Operativo
            $table->string('so_tipo')->nullable();
            $table->string('so_version')->nullable();

            // Hardware
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->decimal('cpu_ghz', 5, 2)->nullable();
            $table->string('cpu_modelo')->nullable();
            $table->integer('ram_gb')->nullable();
            $table->integer('almacenamiento_gb')->nullable();
            $table->string('placa_video')->nullable();

            // Monitor
            $table->string('monitor_marca')->nullable();
            $table->string('monitor_modelo')->nullable();
            $table->decimal('monitor_pulgadas', 4, 1)->nullable();
            $table->enum('monitor_conexion', ['vga', 'hdmi', 'displayport', 'otro'])->nullable();

            // Periféricos
            $table->string('mouse_marca')->nullable();
            $table->string('mouse_modelo')->nullable();
            $table->enum('mouse_conexion', ['usb', 'inalambrico'])->nullable();

            $table->string('teclado_marca')->nullable();
            $table->string('teclado_modelo')->nullable();
            $table->enum('teclado_conexion', ['usb', 'inalambrico'])->nullable();

            $table->string('estabilizador_marca')->nullable();
            $table->string('estabilizador_modelo')->nullable();
            $table->string('estabilizador_color')->nullable();

            // Impresión
            $table->string('impresora_marca')->nullable();
            $table->string('impresora_modelo')->nullable();
            $table->enum('impresora_conexion', ['usb', 'wifi', 'red'])->nullable();

            // Otros dispositivos
            $table->boolean('tiene_camara')->default(false);
            $table->string('camara_marca')->nullable();
            $table->string('camara_modelo')->nullable();
            $table->enum('camara_conexion', ['usb', 'inalambrica'])->nullable();

            $table->boolean('tiene_parlantes')->default(false);
            $table->string('parlantes_marca')->nullable();
            $table->string('parlantes_modelo')->nullable();

            // Software (JSON array)
            $table->json('software_instalado')->nullable();

            // Usuario asignado
            $table->string('usuario_nombre')->nullable();
            $table->string('usuario_apellido')->nullable();
            $table->string('usuario_caracter')->nullable(); // titular, interino, contratado, etc.

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activos');
    }
};
