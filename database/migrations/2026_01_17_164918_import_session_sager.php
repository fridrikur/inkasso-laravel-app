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
        Schema::create('import_session_sager', function (Blueprint $table) {
            $table->id();

            $table->foreignId('import_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sag_id')
                ->constrained('sagers')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['import_session_id', 'sag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_session_sager');
    }
};
