<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebFamilyCardsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('web_family_cards', function(Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('branches');
			$table->string('no_kk')->nullable();
			$table->text('address');
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
		Schema::drop('web_family_cards');
	}
};
