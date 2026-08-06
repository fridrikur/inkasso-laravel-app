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
        Schema::create('sager_konsulent', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sag_id');
            $table->foreign('sag_id')->references('id')->on('sagers')->onDelete('cascade');
            $table->unsignedBigInteger('konsulent_id');
            $table->foreign('konsulent_id')->references('id')->on('konsulenters')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sager_konsulent');
    }
};
