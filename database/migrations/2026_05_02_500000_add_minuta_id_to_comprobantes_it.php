<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comprobantes_it', function (Blueprint $table) {
            $table->foreignId('minuta_id')->nullable()->after('user_id')->constrained('minutas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes_it', function (Blueprint $table) {
            $table->dropForeign(['minuta_id']);
            $table->dropColumn('minuta_id');
        });
    }
};
