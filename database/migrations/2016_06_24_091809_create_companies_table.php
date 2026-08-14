<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name', 45);
            $table->string('company_type', 20);
            $table->char('company_prefix', 2)->nullable();
            $table->string('email', 45)->nullable();
            $table->string('contact_number', 40);
            $table->string('fax_number', 40)->nullable();
            $table->string('address', 250)->nullable();
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
        Schema::drop('companies');
    }
}
