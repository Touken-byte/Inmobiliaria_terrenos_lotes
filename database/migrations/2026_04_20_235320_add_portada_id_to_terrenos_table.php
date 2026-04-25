<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->unsignedBigInteger('portada_id')->nullable()->after('estado_lote');
            $table->foreign('portada_id')->references('id')->on('terreno_imagenes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->dropForeign(['portada_id']);
            $table->dropColumn('portada_id');
        });
    }
};