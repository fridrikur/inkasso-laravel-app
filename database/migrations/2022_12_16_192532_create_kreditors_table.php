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
        Schema::create('kreditors', function (Blueprint $table) {
            $table->id();
            $table->string('navn')->unique();
            $table->integer('lotusID')->unique();
            $table->softDeletes(); // 🟢 Opretter 'deleted_at' kolonnen
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kreditors');
    }
};