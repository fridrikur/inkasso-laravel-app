<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('import_sager', function (Blueprint $table) {
            $table->id();

            // MUST match referenced columns exactly
            $table->unsignedBigInteger('import_id');
            $table->unsignedBigInteger('sag_id');
            $table->unsignedBigInteger('kreditor_id')->nullable();

            $table->timestamps();

            $table->foreign('import_id')
                ->references('id')
                ->on('imports')
                ->cascadeOnDelete();

            $table->foreign('sag_id')
                ->references('id')
                ->on('sagers')
                ->cascadeOnDelete();

            $table->foreign('kreditor_id')
                ->references('id')
                ->on('kreditors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_sager');
    }
};