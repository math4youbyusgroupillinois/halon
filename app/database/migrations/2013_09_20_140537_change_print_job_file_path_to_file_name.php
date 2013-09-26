<?php

use Illuminate\Database\Migrations\Migration;

class ChangePrintJobFilePathToFileName extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('print_jobs', function($table) {
      $table->renameColumn('file_path', 'file_name');
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
      $table->renameColumn('file_name', 'file_path');
    });
	}

}