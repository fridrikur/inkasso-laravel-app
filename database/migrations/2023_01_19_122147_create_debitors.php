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
        Schema::create('debitors', function (Blueprint $table) {
            $table->id();
            $table->string('navn');
            $table->string('co')->nullable();
            $table->string('adresse')->nullable();
            $table->bigInteger('postnr')->nullable();
            $table->string('email')->nullable();
            $table->string('tlf')->nullable();
            $table->string('mobil')->nullable();
            $table->date('adropl')->nullable();
            $table->string('pnr')->nullable();
            $table->longText('kontakt_bemaerkning')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debitors');
    }
};