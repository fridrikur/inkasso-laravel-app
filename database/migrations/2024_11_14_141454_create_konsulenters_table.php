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
        Schema::create('konsulenters', function (Blueprint $table) {
            $table->id();
            $table->string('navn')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('tlf')->nullable()->unique(); 
            $table->string('mobil')->nullable()->unique(); 
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsulenters');
    }
};