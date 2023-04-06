<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRolesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_roles', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unsigned()->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->unsignedBigInteger('role_id')->unsigned()->default(0);
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade');
            $table->date('valid_from');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_roles');
	}
};
