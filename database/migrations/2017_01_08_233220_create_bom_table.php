<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bom_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45)->unique();
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('bom', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 45)->unique();
            $table->string('name', 45)->unique();
            //$table->integer('bom_category_id')->unsigned();
            $table->integer('brand_id')->unsigned();
            $table->tinyInteger('warranty')->default(0)->unsigned();
            $table->integer('quantity')->default(0)->unsigned();
            $table->decimal('suggested_retail_price', 8, 2)->nullable();
            $table->decimal('retail_price', 8, 2)->nullable();
            $table->decimal('dealer_price', 8, 2)->nullable();
            $table->tinyInteger('status')->default(1)->unsigned(); // Active = 1 or Inactive = 2
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            //$table->foreign('bom_category_id')->references('id')->on('bom_categories');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('bom_device_model', function (Blueprint $table) {
            $table->integer('device_model_id')->unsigned();
            $table->integer('bom_id')->unsigned();
            $table->string('category', 45); //BOM or Accessories
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            $table->foreign('device_model_id')
                ->references('id')
                ->on('device_models')
                ->onDelete('cascade');

            $table->foreign('bom_id')
                ->references('id')
                ->on('bom')
                ->onDelete('cascade');

            $table->primary(['device_model_id', 'bom_id']);
        });

        Schema::create('bom_job', function (Blueprint $table) {
            $table->integer('job_id')->unsigned();
            $table->integer('bom_id')->unsigned();

            $table->foreign('job_id')
                ->references('id')
                ->on('jobs')
                ->onDelete('cascade');

            $table->foreign('bom_id')
                ->references('id')
                ->on('bom')
                ->onDelete('cascade');

            $table->primary(['job_id', 'bom_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bom_job');
        Schema::drop('bom_device_model');
        Schema::drop('bom');
        Schema::drop('bom_categories');
    }
}
