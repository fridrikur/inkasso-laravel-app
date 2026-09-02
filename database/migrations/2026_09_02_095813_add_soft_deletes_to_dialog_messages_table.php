<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dialog_messages', function (Blueprint $table) {
            $table->softDeletes(); // 🟢 Tilføjer deleted_at kolonnen
        });
    }

    public function down(): void
    {
        Schema::table('dialog_messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};