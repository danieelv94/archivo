<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventarioDocumentalDetallesTable extends Migration
{
    public function up()
    {
        Schema::create('inventario_documental_detalles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('orden')->default(0);
            $table->string('ubicacion_fisica', 250);
            $table->string('ubicacion_topografica', 250);
            $table->string('no_expediente', 80);
            $table->text('descripcion');
            $table->date('fecha_inicio');
            $table->date('fecha_final')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('inventario_documentales_id');
            $table->unsignedBigInteger('persona_id');

            $table->foreign('inventario_documentales_id','fk_inventario_documental')->on('inventario_documentales')->references('id');
            $table->foreign('persona_id')->on('personas')->references('id');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventario_documental_detalles');
    }
}
