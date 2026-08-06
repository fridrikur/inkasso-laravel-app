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
        Schema::create('kreditor_sagsbehandler', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kreditor_id');
            $table->foreign('kreditor_id')->references('id')->on('kreditors')->onDelete('cascade');
            $table->unsignedBigInteger('sagsbehandler_id');
            $table->foreign('sagsbehandler_id')->references('id')->on('sagsbehandlers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kreditor_sagsbehandler');
    }
};