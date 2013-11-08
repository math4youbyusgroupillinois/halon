<?php

use Illuminate\Database\Migrations\Migration;

class AddTomorrowsMarFileNameToLocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('locations', function($table)
    {
      $table->string('tomorrows_mar_file_name');
    }); 
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::table('locations', function($table) {
      $table->dropColumn('tomorrows_mar_file_name');
    });
	}

}