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
        Schema::create('sagsbehandlers', function (Blueprint $table) {
            $table->id();
            $table->string('navn');
            $table->string('email')->nullable();
            $table->integer('tlf')->nullable();
            $table->integer('mobil')->nullable();
            $table->softDeletes(); // 🟢 Opretter 'deleted_at' kolonnen til SoftDeletes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sagsbehandlers');
    }
};
