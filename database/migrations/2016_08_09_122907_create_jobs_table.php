<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('job_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45);
            $table->string('description', 140);
            $table->tinyInteger('period')->unsigned();
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('job_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45);
            $table->string('description', 140);
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('job_type')->unsigned();
            $table->integer('job_level_id')->unsigned();
            $table->string('case_category', 20);
            $table->integer('company_id')->unsigned();
            $table->string('imei', 19);
            $table->tinyInteger('warranty')->unsigned();
            $table->string('contact_name', 45);
            $table->string('mobile_number', 11);
            $table->string('telephone_number', 11);
            $table->string('image', 20)->nullable();
            $table->string('note', 140)->nullable();
            $table->integer('job_status_id')->unsigned();
            $table->boolean('special_case')->default(false);
            $table->boolean('flag')->default(true);
            $table->date('expire_date');
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('imei')->references('imei')->on('device_inventories');
            $table->foreign('job_level_id')->references('id')->on('job_levels');
            $table->foreign('job_status_id')->references('id')->on('job_statuses');
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('job_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->tinyInteger('job_status_id')->unsigned();
            $table->string('description', 250);
            $table->integer('log_by')->unsigned();
            $table->string('ip_address', 20);
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('jobs');
            $table->foreign('log_by')->references('id')->on('users');
        });

        Schema::create('job_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->integer('route_from')->unsigned();
            $table->integer('route_to')->unsigned();
            $table->integer('accepted_by')->unsigned();
            $table->integer('job_status_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('jobs');
            $table->foreign('route_from')->references('id')->on('companies');
            $table->foreign('route_to')->references('id')->on('companies');
            $table->foreign('accepted_by')->references('id')->on('users');
            $table->foreign('job_status_id')->references('id')->on('job_statuses');
            $table->foreign('created_by')->references('id')->on('users');

            $table->index(['route_to']);
        });

        Schema::create('complaint_job', function (Blueprint $table) {
            $table->integer('complaint_id')->unsigned();
            $table->integer('job_id')->unsigned();

            $table->foreign('complaint_id')->references('id')->on('complaints');
            $table->foreign('job_id')->references('id')->on('jobs');

            $table->primary(['complaint_id', 'job_id']);
        });

        /*Schema::create('job_user', function (Blueprint $table) {
            $table->integer('job_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            $table->foreign('job_id')
                ->references('id')
                ->on('jobs')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['job_id', 'user_id']);
        });*/

        Schema::create('repair_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45)->nullable();
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });

        // WIP: Double check whether join tbl is necessary
        Schema::create('technical_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45)->nullable();
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::create('technical_remarks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45)->nullable();
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::create('job_technicals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->integer('company_id')->unsigned();
            $table->integer('technician_id')->unsigned();
            $table->string('remark', 250)->nullable();
            $table->tinyInteger('void_warranty')->nullable();
            $table->string('status', 15);
            $table->dateTime('acceptance_date')->nullable();
            $table->dateTime('completion_date')->nullable();
            $table->date('expire_date');
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('jobs');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('technician_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('job_qc', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_technical_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->dateTime('acceptance_date')->nullable();
            $table->dateTime('completion_date')->nullable();
            $table->string('remark', 250)->nullable();
            $table->date('expire_date');            
            $table->string('status', 10);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('job_technical_id')->references('id')->on('job_technicals');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
        });

        /*Schema::create('job_technical_repair_type', function (Blueprint $table) {
            $table->integer('job_technical_id')->unsigned();
            $table->integer('repair_type_id')->unsigned();

            $table->foreign('job_technical_id')
                ->references('id')
                ->on('job_technicals')
                ->onDelete('cascade');

            $table->foreign('repair_type_id')
                ->references('id')
                ->on('repair_types')
                ->onDelete('cascade');
        });

        Schema::create('job_technical_part', function (Blueprint $table) {
            $table->integer('job_technical_id')->unsigned();
            $table->integer('technical_part_id')->unsigned();

            $table->foreign('job_technical_id')
                ->references('id')
                ->on('job_technicals')
                ->onDelete('cascade');

            $table->foreign('technical_part_id')
                ->references('id')
                ->on('technical_parts')
                ->onDelete('cascade');
        });*/

        Schema::create('job_technical_remark', function (Blueprint $table) {
            $table->integer('job_technical_id')->unsigned();
            $table->integer('technical_remark_id')->unsigned();

            $table->foreign('job_technical_id')
                ->references('id')
                ->on('job_technicals')
                ->onDelete('cascade');

            $table->foreign('technical_remark_id')
                ->references('id')
                ->on('technical_remarks')
                ->onDelete('cascade');

            //$table->primary(['job_technical_id', 'technical_remark_id']);
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45);
            $table->integer('company_id')->unsigned();
            $table->string('address', 250);
            $table->string('postcode', 10);
            $table->integer('state_id')->unsigned();
            $table->tinyInteger('status')->default(1);
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('job_storage', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->integer('warehouse_id')->unsigned();
            $table->boolean('status')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('job_id')
                ->references('id')
                ->on('jobs')
                ->onDelete('cascade');

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::create('logistics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_from')->unsigned();
            $table->integer('company_to')->unsigned();
            $table->string('waybill_number', 45);
            $table->string('email', 45);
            $table->string('remark', 250);
            $table->string('attention_to', 45);
            $table->string('contact_number', 20);
            $table->string('address', 250);
            $table->string('postcode', 10);
            $table->integer('state_id')->unsigned();
            $table->tinyInteger('status')->default(1);
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('company_from')->references('id')->on('companies');
            $table->foreign('company_to')->references('id')->on('companies');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('job_logistic', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->integer('logistic_id')->unsigned();
            $table->integer('encode_by')->unsigned();
            $table->tinyInteger('status')->default(1);
            $table->boolean('flag')->default(true);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('job_id')
                ->references('id')
                ->on('jobs')
                ->onDelete('cascade');

            $table->foreign('logistic_id')
                ->references('id')
                ->on('logistics')
                ->onDelete('cascade');

            $table->foreign('encode_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('job_logistic');
        Schema::drop('logistics');
        Schema::drop('job_warehouse');
        Schema::drop('warehouses');
        Schema::drop('job_technical_remark');
        /*Schema::drop('job_technical_part');
        Schema::drop('job_technical_repair_type');*/
        Schema::drop('job_qc');
        Schema::drop('job_technicals');
        Schema::drop('technical_remarks');
        Schema::drop('technical_parts');
        Schema::drop('repair_types');
        Schema::drop('job_user');
        Schema::drop('complaint_job');
        Schema::drop('job_routes');
        Schema::drop('job_logs');
        Schema::drop('jobs');
        Schema::drop('job_statuses');
        Schema::drop('job_levels');
    }
}
