<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEWarrantiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_warranties', function (Blueprint $table) {
            $table->increments('id');
            //$table->string('imei', 19)->unique();
            $table->string('imei', 19);
            $table->string('frontliner_code', 10);
            $table->string('model', 25);
            $table->string('name', 50);
            $table->string('email', 40);
            $table->smallInteger('age')->unsigned();
            $table->string('gender', 6);
            $table->string('id_type', 20);
            $table->string('id_number', 30);
            $table->string('mobile_number', 13);
            $table->string('address', 50);
            $table->string('state', 20);
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->unsigned();
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
        Schema::drop('e_warranties');
    }
}
