<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFamilyCardComponentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('family_card_components', function(Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('family_card_id')->unsigned()->nullable();
			$table->foreign('family_card_id')->references('id')->on('family_cards');
			$table->string('status');
			$table->unsignedBigInteger('user_id')->unsigned()->nullable();
			$table->foreign('user_id')->references('id')->on('users');
			$table->integer('sequence');
			$table->string('no_kk_per_user');
			$table->date('valid_until')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('family_card_components');
	}
};
