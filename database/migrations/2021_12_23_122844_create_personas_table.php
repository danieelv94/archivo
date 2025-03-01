<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonasTable extends Migration
{
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('titulo', 10)->nullable();
            $table->string('nombres', 120);
            $table->string('primer_apellido', 120);
            $table->string('segundo_apellido', 120);
            $table->string('email', 120)->unique();
            $table->string('telefono', 120)->nullable();
            $table->string('curp', 18)->unique();
            $table->string('rfc', 18)->nullable()->unique();
            $table->string('sexo')->nullable();
            $table->string('puesto', 250)->nullable();
            $table->string('unidad_presupuestal', 250)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('departamento_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->foreign('departamento_id')->on('departamentos')->references('id');


            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personas');
    }
}
