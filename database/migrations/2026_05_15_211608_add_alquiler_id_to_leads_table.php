<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['terreno_id']);
            $table->dropUnique('unique_active_lead_per_comprador_terreno');
            $table->foreign('terreno_id')->references('id')->on('terrenos')->onDelete('cascade');
            $table->unsignedBigInteger('terreno_id')->nullable()->change();
            $table->foreignId('alquiler_id')->nullable()->constrained('alquileres')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['alquiler_id']);
            $table->dropColumn('alquiler_id');
            $table->unsignedBigInteger('terreno_id')->nullable(false)->change();
            $table->unique(['terreno_id', 'comprador_id', 'estado'], 'unique_active_lead_per_comprador_terreno');
        });
    }
};
