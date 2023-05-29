<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNumberSettingComponentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('number_setting_components', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('number_setting_id')->unsigned()->default(0);
            $table->foreign('number_setting_id')->references('id')->on('number_settings')->onUpdate('cascade');
            $table->integer('sequence');
            $table->string('type');
            $table->string('format');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('number_setting_components');
	}
};
