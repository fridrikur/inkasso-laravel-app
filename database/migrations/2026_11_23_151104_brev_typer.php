<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brev_typer', function (Blueprint $table) {
            $table->id();
            $table->string('navn')->unique('brev_typer_navn_unique');
            $table->integer('sorteringsorden')->default(0);
            $table->boolean('aktiv')->default(true);
            $table->timestamps();
        });

        // Opret et par fornuftige start-typer
        DB::table('brev_typer')->insert([
            ['navn' => 'Standardbrev', 'sorteringsorden' => 1, 'aktiv' => true, 'created_at' => now(), 'updated_at' => now()],
            ['navn' => 'Fuldmagt', 'sorteringsorden' => 2, 'aktiv' => true, 'created_at' => now(), 'updated_at' => now()],
            ['navn' => 'Kontrakttype', 'sorteringsorden' => 3, 'aktiv' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('breve', function (Blueprint $table) {
            $table->foreignId('brevtype_id')
                ->nullable()
                ->after('brevtype')
                ->constrained('brev_typer')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('breve', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brevtype_id');
        });

        Schema::dropIfExists('brev_typer');
    }
};