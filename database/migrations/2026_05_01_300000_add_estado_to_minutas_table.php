<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minutas', function (Blueprint $table) {
            if (!Schema::hasColumn('minutas', 'estado')) {
                $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'completada'])
                      ->default('pendiente')
                      ->after('archivo');
            }
            if (!Schema::hasColumn('minutas', 'observacion')) {
                $table->text('observacion')->nullable()->after('estado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('minutas', function (Blueprint $table) {
            if (Schema::hasColumn('minutas', 'estado')) {
                $table->dropColumn('estado');
            }
            if (Schema::hasColumn('minutas', 'observacion')) {
                $table->dropColumn('observacion');
            }
        });
    }
};