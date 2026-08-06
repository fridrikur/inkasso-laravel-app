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
        Schema::create('import_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // SantanderImport
            $table->foreignId('kreditor_id')->nullable()->constrained()->nullOnDelete();
            $table->json('mapping'); // column → {sager, debitor}
            $table->timestamps();

            $table->unique(['name', 'kreditor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_mappings');
    }
};