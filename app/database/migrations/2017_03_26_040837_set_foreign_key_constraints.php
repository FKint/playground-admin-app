<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetForeignKeyConstraints extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('children', function (Blueprint $table) {
            $table->foreign('age_group_id')->references('id')->on('age_groups');
        });
        Schema::table('families', function (Blueprint $table) {
            $table->foreign('tariff_id')->references('id')->on('tariffs');
        });
        Schema::table('child_families', function (Blueprint $table) {
            $table->foreign('child_id')->references('id')->on('children');
            $table->foreign('family_id')->references('id')->on('families');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('child_family_id')->references('id')->on('child_families');
        });
        Schema::table('activity_list_child_families', function (Blueprint $table) {
            $table->foreign('activity_list_id')->references('id')->on('activity_lists');
            $table->foreign('child_family_id')->references('id')->on('child_families');
        });
        Schema::table('weeks', function (Blueprint $table) {
            $table->foreign('year_id')->references('id')->on('years');
        });
        Schema::table('days', function (Blueprint $table) {
            $table->foreign('week_id')->references('id')->on('weeks');
        });
        Schema::table('week_family_registrations', function (Blueprint $table) {
            $table->foreign('family_id')->references('id')->on('families');
            $table->foreign('week_id')->references('id')->on('weeks');
            $table->foreign('tariff_id')->references('id')->on('tariffs');
        });
        Schema::table('week_registrations', function (Blueprint $table) {
            $table->foreign('child_family_id')->references('id')->on('child_families');
            $table->foreign('week_family_registration_id')->references('id')->on('week_family_registrations');
        });
        Schema::table('day_registrations', function (Blueprint $table) {
            $table->foreign('day_id')->references('id')->on('days');
            $table->foreign('week_registration_id')->references('id')->on('week_registrations');
            $table->foreign('day_part_id')->references('id')->on('day_parts');
        });
        Schema::table('day_registration_supplements', function (Blueprint $table) {
            $table->foreign('supplement_id')->references('id')->on('supplements');
            $table->foreign('day_registration_id')->references('id')->on('day_registrations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('day_registration_supplements', function (Blueprint $table) {
            $table->dropForeign(['supplement_id']);
            $table->dropForeign(['day_registration_id']);
        });
        Schema::table('day_registrations', function (Blueprint $table) {
            $table->dropForeign(['day_id']);
            $table->dropForeign(['week_registration_id']);
            $table->dropForeign(['day_part_id']);
        });
        Schema::table('week_registrations', function (Blueprint $table) {
            $table->dropForeign(['child_family_id']);
            $table->dropForeign(['week_family_registration_id']);
        });
        Schema::table('week_family_registrations', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropForeign(['week_id']);
            $table->dropForeign(['tariff_id']);
        });
        Schema::table('days', function (Blueprint $table) {
            $table->dropForeign(['week_id']);
        });
        Schema::table('weeks', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
        });
        Schema::table('activity_list_child_families', function (Blueprint $table) {
            $table->dropForeign(['activity_list_id']);
            $table->dropForeign(['child_family_id']);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['child_family_id']);
        });
        Schema::table('child_families', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropForeign(['child_id']);
        });
        Schema::table('families', function (Blueprint $table) {
            $table->dropForeign(['tariff_id']);
        });
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['age_group_id']);
        });
    }
}
