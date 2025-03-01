<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreasTable extends Migration
{
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nombre', 120);
            $table->string('siglas', 120)->unique();
            $table->unsignedBigInteger('titular_id')->nullable();
            $table->enum('tipo_area', ['departamento', 'direccion', 'direccion_general', 'organo_interno'] )->default('departamento');
            $table->unsignedBigInteger('area_padre_id')->nullable();


//            $table->foreign('titular_id')
//                ->references('id')
//                ->on('personas')->onDelete('cascade');

            $table->foreign('area_padre_id')
                ->references('id')
                ->on('areas')->onDelete('cascade');



            $table->softDeletes();//ESTABLECER ELIMINACIONES LOGICAS Y NO DEFINITIVAS DE LA TABLA
            $table->timestamps();//AÑADIR LOS CAMPOS created_at y updated_at A LA TABLA
        });
    }

    public function down()
    {
        Schema::dropIfExists('areas');
    }
}
