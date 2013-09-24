<?php

use Illuminate\Database\Migrations\Migration;

class AddLocationIdToPrintJob extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('print_jobs', function($table) {
      $table->integer('location_id');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('print_jobs', function($table) {
      $table->dropColumn('location_id');
    });
	}

}