<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKTRTable extends Migration
{
    public function up()
    {
        Schema::create('ktr', function (Blueprint $table) {
            $table->id();
            $table->string('tekst');
            $table->string('forkortelse');
            $table->timestamps();
        });

    }

    public function down()
    {
         Schema::dropIfExists('ktr');
    }
}