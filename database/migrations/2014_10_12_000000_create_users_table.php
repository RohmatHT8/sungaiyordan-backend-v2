<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('nik')->unique();
            $table->string('no_ktp')->nullable()->unique();
            $table->string('place_of_birth');
            $table->date('date_of_birth');
            $table->string('gender');
            $table->text('ktp_address')->nullable();
            $table->text('address')->nullable();
            $table->string('pos_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('father');
            $table->string('mother');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
