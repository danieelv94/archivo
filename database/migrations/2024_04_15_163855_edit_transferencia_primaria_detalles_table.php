<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transferencia_primaria_detalles', function (Blueprint $table) {
            $table->string('portada_observaciones')->nullable();
            $table->longText('portada_fechas_consulta')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transferencia_primaria_detalles', function (Blueprint $table) {
            $table->dropColumn(['portada_observaciones','portada_fechas_consulta']);
        });
    }
};
