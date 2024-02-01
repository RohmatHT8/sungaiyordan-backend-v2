<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateWidgetPermissionsTable.
 */
class CreateWidgetPermissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('widget_permissions', function(Blueprint $table) {
            $table->id();
            $table->string('ability')->unique();
            $table->unsignedBigInteger('widget_id')->unsigned()->default(0);
            $table->foreign('widget_id')->references('id')->on('widgets')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('widget_permissions');
	}
}
