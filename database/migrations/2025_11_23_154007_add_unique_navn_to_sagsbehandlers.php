<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sagsbehandlers', function (Blueprint $table) {
            // Add a unique constraint to the 'navn' column
            $table->unique('navn');
        });
    }

    public function down(): void
    {
        Schema::table('sagsbehandlers', function (Blueprint $table) {
            // Drop the unique constraint if rolling back
            $table->dropUnique(['navn']);
        });
    }
};
