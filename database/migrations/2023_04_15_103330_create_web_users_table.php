<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('web_users', function(Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('web_user_family_card_id')->unsigned()->nullable();
            $table->foreign('web_user_family_card_id')->references('id')->on('web_family_cards');
			$table->string('name');
			$table->string('father');
			$table->string('mother');
			$table->string('email');
			$table->string('phone_number')->nullable();
			$table->string('nik');
			$table->string('place_of_birth');
			$table->date('date_of_birth');
			$table->date('join_date')->nullable();
			$table->string('gender');
			$table->string('congregational_status');
			$table->string('status_baptize')->nullable();
			$table->date('date_of_baptize')->nullable();
			$table->string('place_of_baptize')->nullable();
			$table->string('who_baptize')->nullable();
			$table->string('status_shdr')->nullable();
			$table->date('date_shdr')->nullable();
			$table->string('place_of_shdr')->nullable();
			$table->string('profession')->nullable();
			$table->text('ktp_address')->nullable();
			$table->string('martial_status');
			$table->date('wedding_date')->nullable();
			$table->string('place_of_wedding')->nullable();
			$table->string('married_church')->nullable();
			$table->string('who_married')->nullable();
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
		Schema::drop('web_users');
	}
};
