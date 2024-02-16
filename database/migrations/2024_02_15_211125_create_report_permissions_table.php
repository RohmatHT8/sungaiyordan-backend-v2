<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateReportPermissionsTable.
 */
class CreateReportPermissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('report_permissions', function(Blueprint $table) {
            $table->id();
            $table->string('ability')->unique();
            $table->unsignedBigInteger('report_id')->unsigned()->default(0);
            $table->foreign('report_id')->references('id')->on('reports')->onUpdate('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('report_permissions');
	}
}
