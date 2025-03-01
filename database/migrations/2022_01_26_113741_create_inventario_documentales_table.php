<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventarioDocumentalesTable extends Migration
{
    public function up()
    {
        Schema::create('inventario_documentales', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('unidad_presupuestal', 250); //fondo
            $table->unsignedBigInteger('area_id');  //unidad administrativa
            $table->string('departamento', 250); // área generadora
            $table->unsignedBigInteger('persona_responsable_id'); // responsable
            $table->integer('anio_captura'); //fecha de reporte
            $table->integer('mes_captura');//fecha de reporte

            $table->string('clave_seccion', 10);
            $table->string('clave_serie', 10);

            $table->dateTime('fecha_cierre_captura')->nullable();

            $table->unsignedBigInteger('persona_revisa_id')->nullable();
            $table->unsignedBigInteger('persona_autoriza_id')->nullable();

            $table->foreign('area_id')->references('id')->on('areas');
            $table->foreign('persona_responsable_id')->references('id')->on('personas');
            $table->foreign('clave_seccion')->references('clave')->on('archivo_secciones');
            // $table->foreign('clave_serie')->references('clave')->on('archivo_series');

            $table->foreign('persona_revisa_id')->references('id')->on('personas');
            $table->foreign('persona_autoriza_id')->references('id')->on('personas');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventario_documentales');
    }
}
