<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transferencia_primarias', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('anio');
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->foreignId('seccion_id')->constrained('archivo_secciones')->cascadeOnDelete();
            $table->foreignId('serie_id')->constrained('archivo_series')->cascadeOnDelete();
            $table->date('fecha_entrega')->nullable();
            $table->string('no_oficio_transferencia')->nullable();
            $table->smallInteger('no_caja')->nullable();
            $table->tinyInteger('estado')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('transferencia_primarias');
    }
};
