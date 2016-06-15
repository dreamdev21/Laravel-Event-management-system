<?php

use Illuminate\Database\Migrations\Migration;

class AddAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function ($table) {
            $table->increments('id');
            $table->string('name', 125);
            $table->integer('visits');
            $table->integer('tickets_sold');
            $table->decimal('sales_volume', 10, 2)->default(0);
            $table->timestamp('last_visit');
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('event_id');
            $table->nullableTimestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('affiliates');
    }
}
