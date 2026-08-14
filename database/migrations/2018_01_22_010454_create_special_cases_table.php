<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_cases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned()->unique();
            $table->string('old_imei', 19);
            $table->string('new_imei', 19)->nullable();
            $table->string('comment', 140)->nullable();
            $table->tinyInteger('status')->default(1); // 1 = New, 2 = Approve, 3 = Denied
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('jobs');
            $table->foreign('old_imei')->references('imei')->on('device_inventories');
            $table->foreign('new_imei')->references('imei')->on('device_inventories');
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
        Schema::drop('special_cases');
    }
}
