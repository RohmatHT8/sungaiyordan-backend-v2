<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalLogsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('approval_logs', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_log_id')->unsigned()->default(0);
            $table->foreign('transaction_log_id')->references('id')->on('transaction_logs')->onUpdate('cascade');
            $table->unsignedBigInteger('approver_id')->unsigned()->default(0);
            $table->foreign('approver_id')->references('id')->on('users')->onUpdate('cascade');
            $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('approval_logs');
	}
};
