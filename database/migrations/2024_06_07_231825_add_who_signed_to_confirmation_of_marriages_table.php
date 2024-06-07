<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWhoSignedToConfirmationOfMarriagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('confirmation_of_marriages', function (Blueprint $table) {
            $table->string('who_signed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('confirmation_of_marriages', function (Blueprint $table) {
            $table->dropColumn(['who_signed']);
        });
    }
}
