<?php

use Illuminate\Database\Migrations\Migration;

class ChangeMarFileNameToTodaysMarFileNameOnLocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('locations', function($table)
    {
        $table->renameColumn('mar_file_name', 'todays_mar_file_name');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::table('locations', function($table)
    {
        $table->renameColumn('todays_mar_file_name', 'mar_file_name');
    });
	}

}