<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rooms', function(Blueprint $table) {
            $table->id();
			$table->string('code')->unique();
			$table->string('name');
			$table->text('note');
			$table->unsignedBigInteger('building_id')->unsigned()->nullable();
            $table->foreign('building_id')->references('id')->on('buildings');
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
		Schema::drop('rooms');
	}
};
