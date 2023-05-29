<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNumberSettingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('number_settings', function(Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->unsignedBigInteger('transaction_id')->unsigned()->default(0);
			$table->foreign('transaction_id')->references('id')->on('transactions')->onUpdate('cascade');
			$table->string('reset_type')->nullable();
			$table->boolean('need_approval')->default(0);
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
		Schema::drop('number_settings');
	}
};
