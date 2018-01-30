<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrganizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name', 100)->unique();
            $table->timestamps();
        });

        Schema::table('years', function (Blueprint $table) {
            $table->string('year', 100)->change();
            $table->renameColumn('year', 'description');
            $table->integer('organization_id')->unsigned();
            $table->foreign('organization_id')->references('id')->on('organizations');
        });
        Schema::table('activity_lists', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->decimal('price', 5, 2)->nullable(false)->default(0)->change();
        });
        Schema::table('admin_sessions', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
        });
        Schema::table('age_groups', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
        });
        Schema::table('children', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign(['year_id', 'age_group_id'])->references(['year_id', 'id'])->on('age_groups');
            $table->index(['year_id', 'id']);
        });
        Schema::table('day_parts', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
        });
        Schema::table('tariffs', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
        });
        Schema::table('families', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign(['year_id', 'tariff_id'])->references(['year_id', 'id'])->on('tariffs');
            $table->index(['year_id', 'id']);
        });
        Schema::table('supplements', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
        });
        Schema::table('week_days', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
        });
        Schema::table('playground_days', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign(['year_id', 'week_id'])->references(['year_id', 'id'])->on('weeks');
            $table->foreign(['year_id', 'week_day_id'])->references(['year_id', 'id'])->on('week_days');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->integer('organization_id')->unsigned()->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations');
        });
        Schema::table('child_families', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign(['year_id', 'child_id'])->references(['year_id', 'id'])->on('children');
            $table->foreign(['year_id', 'family_id'])->references(['year_id', 'id'])->on('families');
            $table->index(['year_id', 'id']);
        });
        Schema::table('activity_list_child_families', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign(['year_id', 'child_family_id'])->references(['year_id', 'id'])->on('child_families');
            $table->foreign(['year_id', 'activity_list_id'])->references(['year_id', 'id'])->on('activity_lists');
        });
        Schema::table('family_week_registrations', function(Blueprint $table){
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign(['year_id', 'family_id'])->references(['year_id', 'id'])->on('families');
            $table->foreign(['year_id', 'week_id'])->references(['year_id', 'id'])->on('weeks');
            $table->foreign(['year_id', 'tariff_id'])->references(['year_id', 'id'])->on('tariffs');
        });
        Schema::table('child_family_day_registrations', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign(['year_id', 'child_id'])->references(['year_id', 'id'])->on('children');
            $table->foreign(['year_id', 'family_id'])->references(['year_id', 'id'])->on('families');
            $table->foreign(['year_id', 'week_id'])->references(['year_id', 'id'])->on('weeks');
            $table->foreign(['year_id', 'week_day_id'])->references(['year_id', 'id'])->on('week_days');
            $table->foreign(['year_id', 'day_part_id'])->references(['year_id', 'id'])->on('day_parts');
            $table->foreign(['year_id', 'age_group_id'])->references(['year_id', 'id'])->on('age_groups');
            $table->index(['year_id', 'id']);
        });
        Schema::table('child_family_day_registration_supplements', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign(['year_id', 'child_family_day_registration_id'], 'year_child_family_day_registration_fk')->references(['year_id', 'id'])->on('child_family_day_registrations');
            $table->foreign(['year_id', 'supplement_id'], 'year_supplement_id_fk')->references(['year_id', 'id'])->on('supplements');
        });
        Schema::table('child_family_week_registrations', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign(['year_id', 'child_id'])->references(['year_id', 'id'])->on('children');
            $table->foreign(['year_id', 'family_id'])->references(['year_id', 'id'])->on('families');
            $table->foreign(['year_id', 'week_id'])->references(['year_id', 'id'])->on('weeks');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('year_id')->unsigned();
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign(['year_id', 'admin_session_id'])->references(['year_id', 'id'])->on('admin_sessions');
            $table->foreign(['year_id', 'family_id'])->references(['year_id', 'id'])->on('families');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropForeign(['year_id', 'admin_session_id']);
            $table->dropForeign(['year_id', 'family_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('child_family_week_registrations', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropForeign(['year_id', 'child_id']);
            $table->dropForeign(['year_id', 'family_id']);
            $table->dropForeign(['year_id', 'week_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('child_family_day_registration_supplements', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropForeign('year_child_family_day_registration_fk');
            $table->dropForeign('year_supplement_id_fk');
            $table->dropColumn('year_id');
        });
        Schema::table('child_family_day_registrations', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropForeign(['year_id', 'child_id']);
            $table->dropForeign(['year_id', 'family_id']);
            $table->dropForeign(['year_id', 'week_id']);
            $table->dropForeign(['year_id', 'week_day_id']);
            $table->dropForeign(['year_id', 'day_part_id']);
            $table->dropForeign(['year_id', 'age_group_id']);
            $table->dropIndex(['year_id', 'id']);
            $table->dropColumn('year_id');
        });
        Schema::table('family_week_registrations', function(Blueprint $table){
            $table->dropForeign(['year_id', 'family_id']);
            $table->dropForeign(['year_id', 'week_id']);
            $table->dropForeign(['year_id', 'tariff_id']);
            $table->dropForeign(['year_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('activity_list_child_families', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropForeign(['year_id', 'child_family_id']);
            $table->dropForeign(['year_id', 'activity_list_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('child_families', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropForeign(['year_id', 'child_id']);
            $table->dropForeign(['year_id', 'family_id']);
            $table->dropIndex(['year_id', 'id']);
            $table->dropColumn('year_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });
        Schema::table('playground_days', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropForeign(['year_id', 'week_id']);
            $table->dropForeign(['year_id', 'week_day_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('week_days', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('supplements', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('families', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropForeign(['year_id', 'tariff_id']);
            $table->dropIndex(['year_id', 'id']);
            $table->dropColumn('year_id');
        });
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('day_parts', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropForeign(['year_id', 'age_group_id']);
            $table->dropIndex(['year_id', 'id']);
            $table->dropColumn('year_id');
        });
        Schema::table('age_groups', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('admin_sessions', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropColumn('year_id');
        });
        Schema::table('activity_lists', function (Blueprint $table) {
            $table->dropForeign(['year_id']);
            $table->dropColumn('year_id');
            $table->decimal('price', 5, 2)->nullable(true)->default(0)->change();
        });
        Schema::table('years', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
            $table->integer('description')->change();
            $table->renameColumn('description', 'year');
        });
        Schema::dropIfExists('organizations');
    }
}
