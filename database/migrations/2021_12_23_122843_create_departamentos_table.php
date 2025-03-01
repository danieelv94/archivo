<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('clave',6);
            $table->string('nombre',100);
            $table->string('iniciales',10);
            $table->unsignedBigInteger('titular_id')->nullable();
            $table->unsignedBigInteger('padre_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->timestamps();

            $table->softDeletes();

//            $table->foreign('titular_id')->on('personas')->references('id');
            $table->foreign('padre_id')->on('departamentos')->references('id');
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
        Schema::dropIfExists('departamentos');
    }
}
