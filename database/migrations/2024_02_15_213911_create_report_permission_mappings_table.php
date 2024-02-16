<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateReportPermissionMappingsTable.
 */
class CreateReportPermissionMappingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('report_permission_mappings', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_permission_id')->unsigned()->default(0);
            $table->foreign('report_permission_id')->references('id')->on('report_permissions')->onUpdate('cascade');
            $table->unsignedBigInteger('role_id')->unsigned()->default(0);
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade');
            $table->unsignedBigInteger('branch_id')->unsigned()->default(0);
            $table->foreign('branch_id')->references('id')->on('branches')->onUpdate('cascade');
            $table->unsignedBigInteger('report_permission_setting_id')->unsigned()->default(0);
            $table->foreign('report_permission_setting_id')->references('id')->on('report_permission_settings')->onUpdate('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('report_permission_mappings');
	}
}
