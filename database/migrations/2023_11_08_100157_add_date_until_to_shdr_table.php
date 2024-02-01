<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateUntilToShdrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shdrs', function (Blueprint $table) {
            $table->date('date_until')->nullable()->after('date_shdr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shdrs', function (Blueprint $table) {
            $table->dropColumn(['date_until']);
        });
    }
}
