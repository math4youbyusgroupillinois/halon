<?php

use Illuminate\Database\Migrations\Migration;

class AddDisplayKeyToLocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('locations', function($table)
    {
      $table->string('display_key');
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
      $table->dropColumn('display_key');
    });
	}

}