<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minutas', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente')->after('archivo');
            $table->text('observacion')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('minutas', function (Blueprint $table) {
            $table->dropColumn(['estado', 'observacion']);
        });
    }
};
