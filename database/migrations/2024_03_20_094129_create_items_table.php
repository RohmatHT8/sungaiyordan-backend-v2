<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('items', function(Blueprint $table) {
            $table->id();
			$table->string('no');
			$table->string('name');
			$table->string('merk')->nullable();
			$table->unsignedBigInteger('item_type_id')->unsigned()->nullable();
            $table->foreign('item_type_id')->references('id')->on('item_types');
			$table->unsignedBigInteger('room_id')->unsigned()->nullable();
            $table->foreign('room_id')->references('id')->on('rooms');
			$table->integer('amount');
			$table->date('date_buying');
			$table->integer('price')->nullable();
			$table->string('note')->nullable();
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
		Schema::drop('items');
	}
};
