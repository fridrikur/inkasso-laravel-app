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
        Schema::create('sag_edit_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sag_id')->constrained('sagers')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();

            $table->string('status')->default('pending');
            // pending | accepted | rejected | cancelled

            $table->timestamp('responded_at')->nullable(); 

            $table->timestamps();

            // ✅ THIS is the important part
            $table->unique(['sag_id', 'requested_by'], 'uniq_sag_user_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sag_edit_requests');
    }
};
