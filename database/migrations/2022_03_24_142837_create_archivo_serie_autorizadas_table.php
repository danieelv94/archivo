<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivoSerieAutorizadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archivo_serie_autorizadas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seccion_id');
            $table->unsignedBigInteger('serie_id');
            $table->unsignedBigInteger('area_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('seccion_id')->on('archivo_secciones')->references('id');
            $table->foreign('serie_id')->on('archivo_series')->references('id');
            $table->foreign('area_id')->on('areas')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archivo_serie_autorizadas');
    }
}
