<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
     {
        Schema::table('cadidos', function (Blueprint $table) {
            $table->boolean('candado')->default(false);
            //$table->boolean('transferencias_parciales');
        });
    }

    public function down(): void
    {
        Schema::table('cadidos', function (Blueprint $table) {
            $table->dropColumn(['candado']);
            //$table->dropColumn(['transferencias_parciales']);
        });
    }
};
