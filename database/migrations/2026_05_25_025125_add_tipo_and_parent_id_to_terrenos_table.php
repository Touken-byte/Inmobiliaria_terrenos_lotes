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
        Schema::table('terrenos', function (Blueprint $table) {
            $table->enum('tipo', ['terreno', 'lote'])->default('terreno')->after('usuario_id');
            $table->foreignId('parent_id')->nullable()->after('tipo')->constrained('terrenos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['tipo', 'parent_id']);
        });
    }
};
