<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kreditor_sager_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kreditor_id')->constrained('kreditors')->onDelete('cascade');
            $table->string('field_name'); // sager table field
            $table->boolean('visible')->default(true);
            $table->boolean('required')->default(false);
            $table->boolean('editable')->default(true);
            $table->string('default_value')->nullable();
            $table->timestamps();

            $table->unique(['kreditor_id','field_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kreditor_sager_fields');
    }
};