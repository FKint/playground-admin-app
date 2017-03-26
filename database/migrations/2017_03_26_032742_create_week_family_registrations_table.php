<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeekFamilyRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('week_family_registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('family_id')->unsigned()->index();
            $table->integer('week_id')->unsigned()->index();
            $table->integer('tariff_id')->unsigned()->index();
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
        Schema::dropIfExists('week_family_registrations');
    }
}
