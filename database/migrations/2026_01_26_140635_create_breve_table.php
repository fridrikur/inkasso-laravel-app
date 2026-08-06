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
            $table->unsignedInteger('brevpos')->default(0);

            $table->string('emne')->nullable();
            $table->longText('tekst');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breve');
    }
};