<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFamilyMemberStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_users', function (Blueprint $table) {
            $table->string('family_member_status')->nullable()->after('web_user_family_card_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_users', function (Blueprint $table) {
            $table->dropColumn(['family_member_status']);
        });
    }
}
