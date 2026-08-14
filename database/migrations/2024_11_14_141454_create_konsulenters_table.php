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
            $table->string('email')->unique();
            $table->string('tlf')->unique(); // Telefonnumre kan godt være string (pga. landekoder/formatering)
            $table->string('mobil')->unique(); 
            
            // Hvis du har lotusID eller andre numre, der skal tjekkes for ledige værdier:
            $table->unsignedInteger('lotusID')->unique()->nullable(); 

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