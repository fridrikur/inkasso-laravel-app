<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('breve', function (Blueprint $table) {
            $table->id();
            $table->string('titel');
            $table->string('brevpos')->default('0'); // Beholder varchar for kompatibilitet med din eksisterende SQL
            $table->string('emne')->nullable();
            $table->mediumText('tekst');
            $table->tinyInteger('beskyttet')->default(0);
            $table->integer('brevtype')->default(0);
            
            // Layout- og marginfelter til rudekuverter / skabelon-finjustering (i piksler)
            $table->integer('margin_top')->default(0);
            $table->integer('margin_left')->default(0);
            $table->integer('margin_right')->default(0);
            $table->float('linjeafstand')->default(1.5);
            $table->integer('right_margin_top')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breve');
    }
};