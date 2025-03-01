<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUbicacionesTopograficas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubicaciones_topograficas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('ubicacion');
            $table->text('bien_mueble')->nullable();
            $table->string('no_inventario')->nullable();
            $table->unsignedBigInteger('departamentos_id');
            $table->timestamps();

            $table->foreign('departamentos_id')->on('departamentos')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ubicaciones_topograficas');
    }
}
