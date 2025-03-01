<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventario_documental_detalles', function (Blueprint $table) {
            $table->boolean('transferido')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('inventario_documental_detalles', function (Blueprint $table) {
            $table->dropColumn(['transferido']);
        });
    }
};
