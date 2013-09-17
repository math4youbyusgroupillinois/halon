<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrintJobs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::create('print_jobs', function(Blueprint $table) {
      $table->increments('id');
      $table->string('file_path');
      $table->string('printer_name');
      $table->timestamps();
    }); 
  }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::drop('print_jobs');
	}

}