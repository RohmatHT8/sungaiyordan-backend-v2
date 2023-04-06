<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionLogsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transaction_logs', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id')->unsigned()->default(0);
            $table->foreign('permission_id')->references('id')->on('permissions')->onUpdate('cascade');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('causer_id')->unsigned()->default(0);
            $table->foreign('causer_id')->references('id')->on('users')->onUpdate('cascade');
            $table->unsignedBigInteger('previous_log_id')->unsigned()->nullable();
            $table->foreign('previous_log_id')->references('id')->on('transaction_logs')->onUpdate('cascade');
            $table->text('new_properties')->nullable();
            $table->boolean('is_active');
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
		Schema::drop('transaction_logs');
	}
};
