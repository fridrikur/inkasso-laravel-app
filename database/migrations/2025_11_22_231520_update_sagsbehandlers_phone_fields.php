<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sagsbehandlers', function (Blueprint $table) {
            // Change from INT → STRING + nullable
            $table->string('tlf')->nullable()->change();
            $table->string('mobil')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('sagsbehandlers', function (Blueprint $table) {
            // Revert back (only if needed)
            $table->integer('tlf')->change();
            $table->integer('mobil')->change();
        });
    }
};
