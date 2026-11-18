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
            $table->string('brevpos')->default('0'); // Beholder varchar for kompatibilitet med eksisterende SQL
            $table->string('emne')->nullable();
            $table->mediumText('tekst');
            $table->tinyInteger('beskyttet')->default(0);
            $table->integer('brevtype')->default(0);
            
            // Standard marginfelter og placeringer i centimeter (decimaler)
            $table->decimal('navn_top_margin', 8, 2)->default(5.45);
            $table->integer('margin_left')->default(0);
            $table->integer('margin_right')->default(0);
            $table->float('linjeafstand')->default(1.5);
            $table->decimal('dato_margin_top', 8, 2)->default(7.57);
            $table->decimal('emne_top_margin', 8, 2)->default(8.91);
            $table->decimal('body_margin_top', 8, 2)->default(1.00);

            // Skriftstørrelser (i punkter / pt)
            $table->decimal('navn_font_size', 5, 2)->default(9.5);
            $table->decimal('emne_font_size', 5, 2)->default(10.5);
            $table->decimal('body_font_size', 5, 2)->default(9.5);

            // Skrifttype
            $table->string('font_family')->default("Century Schoolbook, Georgia, serif");

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breve');
    }
};