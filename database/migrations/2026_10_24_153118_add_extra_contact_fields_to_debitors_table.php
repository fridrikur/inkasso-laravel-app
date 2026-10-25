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
        Schema::table('debitors', function (Blueprint $table) {
            $table->string('email2')->nullable()->after('email');
            $table->string('tlf2')->nullable()->after('tlf');
        });
    }

    public function down(): void
    {
        Schema::table('debitors', function (Blueprint $table) {
            $table->dropColumn(['email2', 'tlf2', 'tlf3']);
        });
    }
};