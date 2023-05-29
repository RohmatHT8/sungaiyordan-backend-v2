<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarriageCertificatesTable extends Migration
{
	public function up()
	{
		Schema::create('marriage_certificates', function(Blueprint $table) {
            $table->id();
			$table->string('no');
			$table->date('date');
			$table->unsignedBigInteger('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('branches');
			$table->string('branch_non_local')->nullable();
			$table->string('location');
			$table->unsignedBigInteger('groom')->unsigned();
            $table->foreign('groom')->references('id')->on('users');
			$table->unsignedBigInteger('bride')->unsigned();
            $table->foreign('bride')->references('id')->on('users');
			$table->string('who_blessed');
            $table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('marriage_certificates');
	}
};
