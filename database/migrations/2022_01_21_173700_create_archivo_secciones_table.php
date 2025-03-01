<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivoSeccionesTable extends Migration
{
    public function up()
    {
        Schema::create('archivo_secciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clave', 10)->unique();
            $table->string('nombre', 150);
            $table->timestamps();
        });

        Schema::create('archivo_series', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clave', 10);
            $table->string('nombre', 150);
            $table->string('clave_seccion', 4);
            $table->unique(['clave', 'clave_seccion']);
            $table->foreign('clave_seccion')->references('clave')->on('archivo_secciones')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('archivo_series');
        Schema::dropIfExists('archivo_secciones');
    }
}
