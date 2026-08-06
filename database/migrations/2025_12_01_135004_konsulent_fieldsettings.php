<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('konsulent_fieldsettings', function (Blueprint $table) {
            $table->id();
            $table->string('field_name');
            $table->string('alias')->nullable();
            $table->boolean('visible')->default(true);
            $table->boolean('required')->default(false);
            $table->boolean('readonly')->default(false);
            $table->json('roles')->nullable();
            $table->string('field_type')->default('text');
            $table->text('description')->nullable();
            $table->string('legacy')->nullable();
            $table->string('section')->default('general');
            $table->integer('column')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['field_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsulent_fieldsettings');
    }
};
