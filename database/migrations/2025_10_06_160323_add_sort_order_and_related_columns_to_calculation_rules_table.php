<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calculation_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('calculation_rules', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('field_name');
            }
            if (!Schema::hasColumn('calculation_rules', 'operation')) {
                $table->string('operation')->default('add')->after('sort_order');
            }
            if (!Schema::hasColumn('calculation_rules', 'related_field')) {
                $table->string('related_field')->nullable()->after('operation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('calculation_rules', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'operation', 'related_field']);
        });
    }
};
