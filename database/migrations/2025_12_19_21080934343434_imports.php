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
        Schema::create('imports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kreditor_id')->nullable()->constrained()->nullOnDelete();

        $table->string('filename')->nullable();

        $table->unsignedInteger('total_rows')->default(0);
        $table->unsignedInteger('successful_rows')->default(0);
        $table->unsignedInteger('failed_rows')->default(0);

        $table->enum('status', ['pending','running','finished','reversed'])
            ->default('pending');

        $table->json('errors')->nullable();

        $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
