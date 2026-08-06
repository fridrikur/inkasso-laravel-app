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
        Schema::create('sagsbehandlers', function (Blueprint $table) {
            $table->id();
            $table->string('navn');
            $table->string('email')->unique();
            $table->integer('tlf')->unique();
            $table->integer('mobil')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sagsbehandlers');
    }
};
