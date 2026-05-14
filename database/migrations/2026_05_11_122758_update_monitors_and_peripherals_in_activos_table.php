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
        Schema::table('activos', function (Blueprint $table) {
            $table->json('monitores')->nullable()->after('placa_video');
            $table->boolean('tiene_mouse')->default(true)->after('monitores');
            $table->boolean('tiene_teclado')->default(true)->after('mouse_conexion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activos', function (Blueprint $table) {
            $table->dropColumn(['monitores', 'tiene_mouse', 'tiene_teclado']);
        });
    }
};
