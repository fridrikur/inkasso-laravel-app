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
        Schema::create('breve_filter', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('brevID');
            $table->tinyInteger('adresse');
            $table->tinyInteger('dato');
            $table->tinyInteger('navn');
            $table->tinyInteger('sagsnr');
            $table->tinyInteger('emne');
            $table->tinyInteger('skjulalle');
            $table->tinyInteger('visalle');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breve_filter');
    }
};
