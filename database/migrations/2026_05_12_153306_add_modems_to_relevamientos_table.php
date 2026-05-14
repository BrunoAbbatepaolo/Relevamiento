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
        Schema::table('relevamientos', function (Blueprint $table) {
            $table->boolean('tiene_modem')->default(false)->after('impresoras');
            $table->json('modems')->nullable()->after('tiene_modem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('relevamientos', function (Blueprint $table) {
            $table->dropColumn(['tiene_modem', 'modems']);
        });
    }
};
