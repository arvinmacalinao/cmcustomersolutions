<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_inventories', function (Blueprint $table) {
            $table->string('imei', 19);
            $table->integer('device_model_id')->unsigned();
            $table->string('color', 45);
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('device_model_id')->references('id')->on('device_models');

            $table->primary('imei');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('device_inventories');
    }
}
