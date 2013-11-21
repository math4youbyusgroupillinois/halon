<?php

use Illuminate\Database\Migrations\Migration;

class AddMarFlagToPrintJobs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('print_jobs', function($table) {
      $table->boolean('mar')->default(false);
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
      $table->dropColumn('mar');
    });
	}

}