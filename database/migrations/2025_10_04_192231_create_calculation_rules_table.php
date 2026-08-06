<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('field_name'); // e.g. hovedstol, renter, gebyr
            $table->enum('type', ['addition', 'deduction', 'target_addition', 'target_deduction']);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculation_rules');
    }
};
