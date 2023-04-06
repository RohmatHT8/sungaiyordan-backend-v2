<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalRolesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('approval_roles', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_id')->unsigned()->default(0);
            $table->foreign('approval_id')->references('id')->on('approvals')->onUpdate('cascade');
            $table->unsignedBigInteger('approver_id')->unsigned()->default(0);
            $table->foreign('approver_id')->references('id')->on('roles')->onUpdate('cascade');
            $table->integer('sequence');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('approval_roles');
	}
};
