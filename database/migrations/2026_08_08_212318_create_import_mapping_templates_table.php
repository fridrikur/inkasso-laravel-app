<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_mapping_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kreditor_id')->nullable()->constrained('kreditors')->nullOnDelete();
            $table->string('navn');
            $table->json('mapping'); // 👈 Sørg for denne er her
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_mapping_templates');
    }
};