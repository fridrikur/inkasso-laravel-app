<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBemaerkningTable extends Migration
{
    public function up()
    {
        Schema::create('bemaerkning', function (Blueprint $table) {
            $table->id();
            $table->string('tekst');
            $table->string('forkortelse')->nullable(); 
            $table->timestamps();
        });

    }

    public function down()
    {
         Schema::dropIfExists('bemaerkning');
    }
}