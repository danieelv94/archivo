<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditInventarioDocumentalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventario_documentales', function (Blueprint $table) {
            $table->dropColumn('departamento');
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
        Schema::table('inventario_documentales', function (Blueprint $table) {
            $table->dropColumn('departamento_id');
            $table->string('departamento', 250); // área generadora
            $table->dropForeign('inventario_documentales_departamento_id_foreign');
        });
    }
}
