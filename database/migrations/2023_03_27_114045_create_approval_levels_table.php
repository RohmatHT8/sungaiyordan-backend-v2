<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalLevelsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('approval_levels', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_id')->unsigned()->default(0);
            $table->foreign('approval_id')->references('id')->on('approvals')->onUpdate('cascade');
            $table->integer('level_diff');
            $table->integer('level_count');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('approval_levels');
	}
};
