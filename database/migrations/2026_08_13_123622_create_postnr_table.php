<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postnr', function (Blueprint $table) {
            $table->id();
            $table->integer('postnr')->unique();
            $table->string('by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postnr');
    }
};