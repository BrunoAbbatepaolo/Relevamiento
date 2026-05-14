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
        Schema::table('servidores', function (Blueprint $table) {
            $table->string('ram_tipo')->nullable()->after('cpu_ghz');
            $table->json('almacenamiento_discos')->nullable()->after('almacenamiento_gb');
        });

        Schema::table('activos', function (Blueprint $table) {
            $table->string('ram_tipo')->nullable()->after('cpu_modelo');
            $table->json('almacenamiento_discos')->nullable()->after('almacenamiento_gb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servidores', function (Blueprint $table) {
            $table->dropColumn(['ram_tipo', 'almacenamiento_discos']);
        });

        Schema::table('activos', function (Blueprint $table) {
            $table->dropColumn(['ram_tipo', 'almacenamiento_discos']);
        });
    }
};
