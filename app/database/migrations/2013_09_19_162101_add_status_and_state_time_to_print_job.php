<?php

use Illuminate\Database\Migrations\Migration;

class AddStatusAndStateTimeToPrintJob extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('print_jobs', function($table) {
      $table->boolean('enque_status');
      $table->boolean('enque_timestamp');
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
      $table->drop('enque_status');
      $table->drop('enque_timestamp');
    });
	}

}