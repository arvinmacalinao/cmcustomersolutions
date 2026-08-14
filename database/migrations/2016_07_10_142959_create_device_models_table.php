<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->unique();
            $table->string('image', 45)->nullable();
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::create('device_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 30)->unique();
            $table->string('name', 30)->nullable();
            $table->integer('brand_id')->unsigned();
            $table->integer('device_type_id')->unsigned();
            $table->tinyInteger('warranty')->unsigned();
            $table->decimal('price', 8, 2);
            $table->decimal('labor_cost_1', 8, 2);
            $table->decimal('labor_cost_2', 8, 2);
            $table->decimal('labor_cost_3', 8, 2);
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('device_type_id')->references('id')->on('device_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('device_types');
        Schema::drop('device_models');
    }
}
