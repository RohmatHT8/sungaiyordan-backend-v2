<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShdrsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shdrs', function(Blueprint $table) {
            $table->id();
			$table->string('no');
			$table->date('date_shdr');
			$table->unsignedBigInteger('place_of_shdr')->unsigned();
            $table->foreign('place_of_shdr')->references('id')->on('branches');
			$table->unsignedBigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
			$table->string('who_signed');
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
		Schema::drop('shdrs');
	}
};
