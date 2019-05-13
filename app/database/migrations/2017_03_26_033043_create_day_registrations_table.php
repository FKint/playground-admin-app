<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDayRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('day_registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('day_id')->unsigned()->index();
            $table->integer('week_registration_id')->unsigned()->index();
            $table->integer('day_part_id')->unsigned()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('day_registrations');
    }
}
