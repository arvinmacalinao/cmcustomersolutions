<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_registrations', function (Blueprint $table) {
            $table->string('imei', 19)->unique();
            $table->integer('customer_id')->unsigned();
            $table->string('pop_ref', 100);
            $table->date('pop_date');
            $table->date('warranty_date');
            $table->tinyInteger('warranty_status')->default(1);
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('imei')->references('imei')->on('device_inventories');

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
        Schema::drop('device_registrations');
    }
}
