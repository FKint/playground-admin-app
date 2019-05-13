<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgeGroupAndAttendedFieldsToDayRegistrations extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('child_family_day_registrations', function (Blueprint $table) {
            $table->boolean('attended')->default(false);
            $table->integer('age_group_id')->unsigned()->default(1)->index();
            $table->foreign('age_group_id')->references('id')->on('age_groups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('child_family_day_registrations', function (Blueprint $table) {
            $table->dropForeign(['age_group_id']);
            $table->dropColumn(['age_group_id', 'attended']);
        });
    }
}
