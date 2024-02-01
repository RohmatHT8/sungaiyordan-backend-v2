<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWidgetPermissionSettingIdToWidgetPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_permission_mappings', function (Blueprint $table) {
            $table->unsignedBigInteger('widget_permission_setting_id')->unsigned()->default(0);
            $table->foreign('widget_permission_setting_id')->references('id')->on('widget_permission_settings')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('widget_permission_mappings', function (Blueprint $table) {
            $table->dropForeign(['widget_permission_setting_id']);
            $table->dropColumn(['widget_permission_setting_id']);
        });
    }
}
