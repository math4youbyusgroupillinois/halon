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
      $table->string('enque_failure_message');
      $table->date('enque_timestamp');
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
       $table->dropColumn('enque_failure_message');
    });

    Schema::table('print_jobs', function($table) {
       $table->dropColumn('enque_timestamp');
     });
	}

}