<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaptismsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('baptisms', function(Blueprint $table) {
            $table->id();
			$table->string('no');
			$table->date('date');
			$table->unsignedBigInteger('place_of_baptism_inside')->unsigned()->nullable();
            $table->foreign('place_of_baptism_inside')->references('id')->on('branches');
			$table->string('place_of_baptism_outside')->nullable();
			$table->unsignedBigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
			$table->string('who_baptism');
            $table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('baptisms');
	}
};
