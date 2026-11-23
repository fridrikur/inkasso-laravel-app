<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('breve')) {
            $duplicates = DB::table('breve')
                ->select('titel', DB::raw('COUNT(*) as cnt'))
                ->whereNotNull('titel')
                ->groupBy('titel')
                ->having('cnt', '>', 1)
                ->pluck('titel');

            if ($duplicates->isNotEmpty()) {
                throw new RuntimeException(
                    'Kan ikke oprette unikt indeks på breve.titel — dubletter fundet: '
                    . $duplicates->implode(', ')
                );
            }
        }

        Schema::create('breve', function (Blueprint $table) {
            $table->id();
            $table->string('titel')->unique('breve_titel_unique');
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
            $table->decimal('brevhoved_top_margin', 8, 2)->default(1.50);

            // Skriftstørrelser (i punkter / pt)
            $table->decimal('navn_font_size', 5, 2)->default(9.5);
            $table->decimal('emne_font_size', 5, 2)->default(10.5);
            $table->decimal('body_font_size', 5, 2)->default(9.5);

            // Skrifttype
            $table->string('font_family')->default("Century Schoolbook, Georgia, serif");

            //firma brevhoved
            $table->boolean('visfirmabrevhoved')->default(true);
            $table->string('firma_navn')->default('DANSK KONSULENT GRUPPE A/S');
            $table->string('firma_adresse')->default('SCT. HANSGADE 9, 1 SAL, 4100 RINGSTED');
            $table->string('firma_telefon')->default('+45 22 22 68 94');
            $table->string('firma_mail')->default('DKG@DKG-APS.DK');
            $table->string('firma_web')->default('WWW.DKG-APS.DK');
            $table->string('firma_cvr')->default('21 24 72 86');

            $table->decimal('firma_font_size', 5, 2)->default(8.5);
            $table->decimal('firma_left_margin', 8, 2)->default(11.80);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breve');
    }
};