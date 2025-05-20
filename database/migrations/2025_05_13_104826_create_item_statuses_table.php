<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('item_statuses', function(Blueprint $table) {
            $table->id();
			$table->string('status');
			$table->unsignedBigInteger('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('items');
			$table->unsignedBigInteger('room_id')->unsigned()->nullable();
            $table->foreign('room_id')->references('id')->on('rooms');
			$table->date('date')->nullable();
			$table->string('note')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('item_statuses');
	}
};
