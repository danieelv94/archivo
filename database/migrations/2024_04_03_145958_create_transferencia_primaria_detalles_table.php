<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transferencia_primaria_detalles', function (Blueprint $table) {
            $table->id();
//            $table->integer('orden')->default(0);
            $table->unsignedBigInteger('transferencia_primaria_id');
            $table->unsignedBigInteger('inventario_documental_detalle_id');
            $table->string('no_expediente_legajo',80);
            $table->tinyInteger('no_legajo')->nullable();

            $table->smallInteger('no_fojas')->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_final')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->foreign('transferencia_primaria_id','fk_transferencia_primaria')->on('transferencia_primarias')->references('id')->cascadeOnDelete();
            $table->foreign('inventario_documental_detalle_id','fk_inventario_documental_detalles')->on('inventario_documental_detalles')->references('id')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('transferencia_primaria_detalles');
    }
};
