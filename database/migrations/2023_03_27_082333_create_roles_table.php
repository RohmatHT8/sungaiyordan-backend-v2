<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles', function(Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('department_id')->unsigned()->default(0);
            $table->foreign('department_id')->references('id')->on('departments')->onUpdate('cascade');
            $table->unsignedBigInteger('boss_id')->unsigned()->nullable();
            $table->foreign('boss_id')->references('id')->on('roles')->onUpdate('cascade');
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
		Schema::drop('roles');
	}
};
