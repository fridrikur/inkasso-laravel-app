<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brev_felt_indstillinger', function (Blueprint $table) {
            $table->id();
            $table->string('felt_navn')->unique();
            $table->string('visnings_navn')->nullable(); // F.eks. "Debitors Navn"
            $table->string('kategori')->default('Generelt'); // F.eks. 'Sag', 'Debitor', 'System'
            $table->boolean('aktiv')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brev_felt_indstillinger');
    }
};