<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('branches', function(Blueprint $table) {
            $table->id();
			$table->string('code');
            $table->string('name');
            $table->text('address');
            $table->string('telephone')->nullable();
			$table->unsignedBigInteger('shepherd_id')->unsigned()->nullable();
            $table->foreign('shepherd_id')->references('id')->on('users')->onUpdate('cascade');
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
		Schema::drop('branches');
	}
};
