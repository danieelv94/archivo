<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivoCalendariosTable extends Migration
{
    public function up()
    {
        Schema::create('archivo_calendarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('persona_id');

            $table->integer('anio_captura');
            $table->integer('mes_captura');
            $table->dateTime('fecha_hora_limite_captura');

            $table->foreign('persona_id')->references('id')->on('personas');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('archivo_calendarios');
    }
}
