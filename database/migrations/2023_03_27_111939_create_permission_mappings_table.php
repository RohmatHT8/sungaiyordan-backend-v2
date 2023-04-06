<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionMappingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permission_mappings', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id')->unsigned()->default(0);
            $table->foreign('permission_id')->references('id')->on('permissions')->onUpdate('cascade');
            $table->unsignedBigInteger('role_id')->unsigned()->default(0);
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade');
            $table->unsignedBigInteger('branch_id')->unsigned()->default(0);
            $table->foreign('branch_id')->references('id')->on('branches')->onUpdate('cascade');
            $table->unsignedBigInteger('permission_setting_id')->unsigned()->default(0);
            $table->foreign('permission_setting_id')->references('id')->on('permission_settings')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('permission_mappings');
	}
};
