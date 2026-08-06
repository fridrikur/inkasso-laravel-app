<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sagsbehandlers', function (Blueprint $table) {

            // 1) Drop existing unique indexes (they are causing the MySQL errors)
            try { $table->dropUnique('sagsbehandlers_tlf_unique'); } catch (\Exception $e) {}
            try { $table->dropUnique('sagsbehandlers_mobil_unique'); } catch (\Exception $e) {}

            // 2) Recreate unique constraints that allow NULLs
            $table->string('tlf')->nullable()->change();
            $table->string('mobil')->nullable()->change();

            // Add fresh indexes
            $table->unique('tlf');
            $table->unique('mobil');
        });
    }

    public function down()
    {
        Schema::table('sagsbehandlers', function (Blueprint $table) {

            // Reverse to non-null integers (if needed)
            $table->dropUnique(['tlf']);
            $table->dropUnique(['mobil']);

            $table->integer('tlf')->nullable(false)->change();
            $table->integer('mobil')->nullable(false)->change();
        });
    }
};
