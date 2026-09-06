<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sager_kreditor', function (Blueprint $table) {
            $table->index(['kreditor_id', 'sag_id'], 'idx_kreditor_sag');
        });

        Schema::table('kreditor_user', function (Blueprint $table) {
            $table->index(['kreditor_id', 'user_id'], 'idx_kreditor_user');
        });

        Schema::table('kreditor_sagsbehandler', function (Blueprint $table) {
            $table->index(['kreditor_id', 'sagsbehandler_id'], 'idx_kreditor_sb');
        });
    }

    public function down(): void
    {
        Schema::table('sager_kreditor', function (Blueprint $table) {
            $table->dropIndex('idx_kreditor_sag');
        });

        Schema::table('kreditor_user', function (Blueprint $table) {
            $table->dropIndex('idx_kreditor_user');
        });

        Schema::table('kreditor_sagsbehandler', function (Blueprint $table) {
            $table->dropIndex('idx_kreditor_sb');
        });
    }
};