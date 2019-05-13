<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDayRegistrationSupplementsTable extends Migration
{
    /**
     * Run the migrations.
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
     */
    public function down()
    {
        Schema::dropIfExists('day_registration_supplements');
    }
}
