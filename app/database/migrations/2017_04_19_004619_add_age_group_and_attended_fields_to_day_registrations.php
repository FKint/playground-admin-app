<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAgeGroupAndAttendedFieldsToDayRegistrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('child_family_day_registrations', function (Blueprint $table) {
            $table->boolean('attended')->default(false);
            $table->integer('age_group_id')->unsigned()->index();
            $table->foreign('age_group_id')->references('id')->on('age_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('child_family_day_registrations', function (Blueprint $table) {
            $table->dropForeign(['age_group_id']);
            $table->dropIndex(['age_group_id']);
            $table->dropColumn('age_group_id');
            $table->dropColumn('attended');
        });
    }
}
