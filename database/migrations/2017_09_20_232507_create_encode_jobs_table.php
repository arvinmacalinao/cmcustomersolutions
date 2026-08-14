<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncodeJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encode_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_logistic_id')->unsigned();
            $table->string('description', 250);
            $table->boolean('status')->nullable(); // Pass or Fail
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('job_logistic_id')->references('id')->on('job_logistic');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('encode_jobs');
    }
}
