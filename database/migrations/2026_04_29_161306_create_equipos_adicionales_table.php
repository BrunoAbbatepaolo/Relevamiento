<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos_adicionales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relevamiento_id')->constrained('relevamientos')->cascadeOnDelete();

            // Proyector
            $table->boolean('tiene_proyector')->default(false);
            $table->string('proyector_marca')->nullable();
            $table->enum('proyector_conexion', ['usb', 'inalambrico', 'hdmi'])->nullable();
            $table->string('proyector_id_inventario')->nullable();

            // Pantalla LED
            $table->boolean('tiene_led')->default(false);
            $table->string('led_marca')->nullable();
            $table->decimal('led_pulgadas', 4, 1)->nullable();
            $table->enum('led_conexion', ['usb', 'inalambrico', 'hdmi'])->nullable();
            $table->string('led_id_inventario')->nullable();

            // Cámara (videoconferencia)
            $table->boolean('tiene_camara')->default(false);
            $table->string('camara_marca')->nullable();
            $table->enum('camara_conexion', ['usb', 'inalambrica'])->nullable();
            $table->string('camara_id_inventario')->nullable();

            // Cámaras de vigilancia
            $table->integer('cantidad_camaras_vigilancia')->default(0);
            $table->json('camaras_vigilancia_ids')->nullable();

            // Otros dispositivos (JSON array de objetos {descripcion, id_inventario})
            $table->json('otros_dispositivos')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos_adicionales');
    }
};
