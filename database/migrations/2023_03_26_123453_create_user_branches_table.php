<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBranchesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_branches', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unsigned()->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->unsignedBigInteger('branch_id')->unsigned()->default(0);
            $table->foreign('branch_id')->references('id')->on('branches')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_branches');
	}
};
