<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('firmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->integer('tipo_documento');
            $table->text('elaboro')->nullable();
            $table->text('valido')->nullable();
            $table->text('recibio')->nullable();
            $table->text('autorizo')->nullable();
            $table->text('reviso')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firmas');
    }
};
