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
		Schema::create('budgets', function (Blueprint $table) {
			$table->increments('id');
			$table->string('note');
			$table->string('amount');
			$table->date('date');
			$table->unsignedBigInteger('role_id')->unsigned()->nullable();
			$table->foreign('role_id')->references('id')->on('roles');
			$table->unsignedBigInteger('branch_id')->unsigned()->nullable();
			$table->foreign('branch_id')->references('id')->on('branches');
			$table->boolean('is_closed')->default(false);
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
		Schema::drop('budgets');
	}
};
