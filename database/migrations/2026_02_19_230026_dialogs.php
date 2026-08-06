<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dialogs', function (Blueprint $table) {

            $table->id();

            $table->longText('tekst');

            $table->timestamp('dato')->useCurrent();

            $table->enum('kategori', [
                'bogholderi',
                'historik',
                'klientinformation'
            ]);

            // Relations
            $table->foreignId('sag_id')
                ->constrained('sagers')
                ->cascadeOnDelete();

            $table->foreignId('kreditor_id')
                ->constrained('kreditors')
                ->cascadeOnDelete();

            $table->foreignId('konsulent_id')
                ->constrained('konsulenters')
                ->cascadeOnDelete();

            $table->foreignId('sagsbehandler_id')
                ->nullable()
                ->constrained('sagsbehandlers')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialogs');
    }
};
