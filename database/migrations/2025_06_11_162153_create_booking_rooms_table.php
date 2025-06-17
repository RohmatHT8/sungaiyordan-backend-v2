<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('booking_rooms', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedBigInteger('user_id')->unsigned()->nullable();
			$table->foreign('user_id')->references('id')->on('users');
			$table->unsignedBigInteger('branch_id')->unsigned()->nullable();
			$table->foreign('branch_id')->references('id')->on('branches');
			$table->string('user')->nullable();
			$table->string('whereof')->nullable();
			$table->date('date');
			$table->date('date_until');
			$table->string('used_for');
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
		Schema::drop('booking_rooms');
	}
};
