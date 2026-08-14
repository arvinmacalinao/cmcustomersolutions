<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name', 45)->unique();
            $table->string('company_type', 20);
            $table->char('company_prefix', 2)->nullable();
            $table->string('email', 45)->nullable();
            $table->string('contact_number', 40);
            $table->string('fax_number', 40)->nullable();
            $table->string('address', 250)->nullable();
            $table->string('state', 45);
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::create('temp_device_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 30)->unique();
            $table->string('name', 30)->nullable();
            $table->string('brands', 30);
            $table->string('device_type', 30);
            $table->tinyInteger('warranty')->unsigned();
            $table->decimal('price', 8, 2);
            $table->decimal('labor_cost_1', 8, 2);
            $table->decimal('labor_cost_2', 8, 2);
            $table->decimal('labor_cost_3', 8, 2);
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            //$table->foreign('brand_id')->references('id')->on('brands');
            //$table->foreign('device_type_id')->references('id')->on('device_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('temp_companies');
        Schema::drop('temp_device_models');
    }
}
