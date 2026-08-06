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
          Schema::create('sagers', function (Blueprint $table) {

            $table->id(); // REQUIRED
            $table->string('sagsnr');
            //datofelter
            $table->date('afsluttet')->nullable()->default(null);
            $table->date('faktureret')->nullable()->default(null);
            $table->date('betalt')->nullable()->default(null);
            $table->date('fakturadato')->nullable()->default(null);
            $table->date('modtaget')->nullable()->default(null);
            $table->date('senesterapport')->nullable()->default(null);
            $table->date('opgivet')->nullable()->default(null);
            $table->string('fakturanr')->nullable(); // or ->default('') if you prefer
            //beløb
            $table->string('hovedstol')->nullable();
            $table->string('renter')->nullable()->default(null);
            $table->string('gebyr')->nullable()->default(null);
            $table->string('ialt')->nullable()->default(null);
            $table->string('startgebyr')->nullable()->default(null);
            $table->string('restgaeld_dkg')->nullable()->default(null);
            $table->longText('kort_bemaerkning')->nullable()->default(null);
            $table->string('indbetalt')->nullable()->default(null);
            $table->string('n_mdlydelse')->nullable()->default(null);
            $table->string('stelnr')->nullable()->default(null);
            $table->longText('aktiv')->nullable()->default(null);
            $table->string('kode')->nullable()->default(null);
            $table->date('dato')->nullable()->default(null);
            $table->string('restgaeld_kreditor', 50)->nullable();
            $table->timestamps();
          });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sagers');
    }
};