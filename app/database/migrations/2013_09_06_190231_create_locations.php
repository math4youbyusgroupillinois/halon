<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('locations', function(Blueprint $table)
    {
      $table->increments('id');
      $table->string('description');
      $table->string('phone_number');
      $table->string('printer_name');
      $table->string('mar_file_name');
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
    Schema::drop('locations');
	}

}