<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationFieldsToFamilyCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('family_cards', function (Blueprint $table) {
            $table->string('city')->nullable(false); // Kota
            $table->string('subdistrict')->nullable(false); // Kelurahan
            $table->string('postal_code', 10)->nullable(false); // Kode Pos
            $table->string('rtrw', 20)->nullable(false); // RT/RW
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('family_cards', function (Blueprint $table) {
            $table->dropColumn(['city', 'subdistrict', 'postal_code', 'rtrw']);
        });
    }
}
