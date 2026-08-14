<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45);
            $table->string('email', 45)->nullable()->unique();
            $table->string('gender', 6);
            $table->date('dob')->nullable();
            // TODO: Switch to non-nullable during migration
            /*$table->string('id_type', 20);
            $table->string('id_number', 30);*/
            $table->string('id_type', 20)->nullable();
            $table->string('id_number', 30)->nullable();
            $table->string('mobile_number', 20)->nullable();
            $table->string('home_number', 20)->nullable();
            $table->string('fax_number', 20)->nullable();
            $table->string('address', 250)->nullable();
            $table->string('postcode', 8)->nullable();
            $table->integer('state_id')->unsigned();
            $table->integer('country_id')->unsigned();
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customers');
    }
}
