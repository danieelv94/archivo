<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivoSupervisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archivo_supervisions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('area_id');
            $table->integer('anio_captura');
            $table->integer('mes_captura');
            /**
             *  0 = No capturo datos
             *  1 = Completo
             *  2 = Incompleto
             */
            $table->integer('semaforo');


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
        Schema::dropIfExists('archivo_supervisions');
    }
}
