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
            $table->json('impresoras')->nullable()->after('tiene_impresora');
            $table->dropColumn(['impresora_marca', 'impresora_modelo', 'impresora_escaner', 'impresora_tipo']);
        });
    }

    public function down(): void
    {
        Schema::table('relevamientos', function (Blueprint $table) {
            $table->dropColumn('impresoras');
            $table->string('impresora_marca')->nullable();
            $table->string('impresora_modelo')->nullable();
            $table->boolean('impresora_escaner')->default(false);
            $table->string('impresora_tipo')->nullable();
        });
    }
};
