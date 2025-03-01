<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivoConfigsTable extends Migration
{
    public function up()
    {
        Schema::create('archivo_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('captura_abierta')->default(true);
            $table->timestamps();
        });


    }

    public function down()
    {
        Schema::dropIfExists('archivo_configs');
    }
}
