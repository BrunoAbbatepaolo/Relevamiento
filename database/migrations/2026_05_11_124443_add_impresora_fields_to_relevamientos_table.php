<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('relevamientos', function (Blueprint $table) {
            $table->boolean('tiene_impresora')->default(false)->after('oficina_id');
            $table->string('impresora_marca')->nullable()->after('tiene_impresora');
            $table->string('impresora_modelo')->nullable()->after('impresora_marca');
            $table->boolean('impresora_escaner')->nullable()->after('impresora_modelo');
            $table->string('impresora_tipo')->nullable()->after('impresora_escaner'); // tinta o toner
        });
    }

    public function down(): void
    {
        Schema::table('relevamientos', function (Blueprint $table) {
            $table->dropColumn([
                'tiene_impresora',
                'impresora_marca',
                'impresora_modelo',
                'impresora_escaner',
                'impresora_tipo',
            ]);
        });
    }
};
