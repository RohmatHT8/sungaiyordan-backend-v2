<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemBranchesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('item_branches', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->unsigned()->default(0);
            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade');
            $table->unsignedBigInteger('branch_id')->unsigned()->default(0);
            $table->foreign('branch_id')->references('id')->on('branches')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('item_branches');
	}
};
