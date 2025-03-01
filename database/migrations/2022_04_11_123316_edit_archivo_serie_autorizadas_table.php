<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditArchivoSerieAutorizadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archivo_serie_autorizadas', function (Blueprint $table) {
            $table->unsignedBigInteger('departamento_id');
            $table->foreign('departamento_id')->references('id')->on('departamentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('archivo_serie_autorizadas', function (Blueprint $table) {
            $table->dropColumn('departamento_id');
            $table->dropForeign('archivo_serie_autorizadas_departamento_id_foreign');
        });
    }
}
