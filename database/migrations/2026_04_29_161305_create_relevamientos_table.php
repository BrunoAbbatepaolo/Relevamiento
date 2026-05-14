<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relevamientos', function (Blueprint $table) {
            $table->id();

            // ¡CAMBIO AQUÍ! 
            // Usamos unsignedBigInteger sin constrained() porque estas tablas están en el servidor externo.
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('oficina_id');

            $table->string('responsable_nombre');
            $table->enum('estado', ['borrador', 'completado', 'revisado'])->default('borrador');

            // Este SÍ lleva constrained() porque la tabla 'users' sí está en tu BD local
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relevamientos');
    }
};
