<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('approvals', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id')->unsigned()->default(0);
            $table->foreign('permission_id')->references('id')->on('permissions')->onUpdate('cascade');
            $table->unsignedBigInteger('branch_id')->unsigned()->default(0);
            $table->foreign('branch_id')->references('id')->on('branches')->onUpdate('cascade');
            $table->string('requirement');
            $table->string('based_on');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('approvals');
	}
};
