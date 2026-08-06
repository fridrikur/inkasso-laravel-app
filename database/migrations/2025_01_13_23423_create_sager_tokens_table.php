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
        Schema::create('sager_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('token_id');
            $table->foreign('token_id')->references('id')->on('tokens')->onDelete('cascade');
            $table->unsignedBigInteger('sag_id');
            $table->foreign('sag_id')->references('id')->on('sagers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sager_tokens');
    }
};
