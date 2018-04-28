<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayRegistrationSupplementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_registration_supplements', function (Blueprint $table) {
            $table->integer('day_registration_id')->unsigned()->index();
            $table->integer('supplement_id')->unsigned()->index();
            $table->primary(['day_registration_id', 'supplement_id'], 'registration_supplement_primary');
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
        Schema::dropIfExists('day_registration_supplements');
    }
}