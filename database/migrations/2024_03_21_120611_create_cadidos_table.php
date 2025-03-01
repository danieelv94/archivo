<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cadidos', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('anio');
            $table->foreignId('seccion_id')->constrained('archivo_secciones')->cascadeOnDelete();
            $table->foreignId('serie_id')->constrained('archivo_series')->cascadeOnDelete();

            $table->tinyInteger('valor_primario_administrativa')->nullable();
            $table->tinyInteger('valor_primario_fiscal')->nullable();
            $table->tinyInteger('valor_primario_legal')->nullable();

            $table->boolean('valor_secundario_informativo')->default(false);
            $table->boolean('valor_secundario_evidencial')->default(false);
            $table->boolean('valor_secundario_testimonial')->default(false);

            $table->tinyInteger('tiempo_guarda_tramite');
            $table->tinyInteger('tiempo_guarda_concentracion');

            $table->longText('fundamento_legal');

            $table->boolean('clasificacion_publica')->default(false);
            $table->boolean('clasificacion_reservada')->default(false);
            $table->boolean('clasificacion_confidencial')->default(false);

            $table->enum('destino_final',['B','AH','M']);

            $table->longText('particularidades')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cadidos');
    }
};
