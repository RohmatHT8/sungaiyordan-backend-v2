<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUserWidgetsTable.
 */
class CreateUserWidgetsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_widgets', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unsigned()->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->unsignedBigInteger('widget_id')->unsigned()->default(0);
            $table->foreign('widget_id')->references('id')->on('widgets')->onUpdate('cascade');
            $table->boolean('show')->default(true);
			$table->integer('sequence')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_widgets');
	}
}
