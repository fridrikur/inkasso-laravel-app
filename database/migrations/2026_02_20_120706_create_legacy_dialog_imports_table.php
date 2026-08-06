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
        Schema::create('legacy_dialog_imports', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('legacy_id');
            $table->unsignedBigInteger('legacy_sag_id');

            $table->longText('tekst');
            $table->timestamp('dato');

            $table->string('username');   // old brugernavn
            $table->string('type');       // historik, bogholderi, klientinformation

            $table->boolean('processed')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_dialog_imports');
    }
};