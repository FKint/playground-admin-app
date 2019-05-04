<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRegistrationsStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists("day_registration_supplements");
        Schema::dropIfExists("day_registrations");
        Schema::dropIfExists("week_registrations");
        Schema::dropIfExists("week_family_registrations");
        Schema::dropIfExists("days");
        Schema::dropIfExists("weeks");

        Schema::table('child_families', function (Blueprint $table) {
            $table->unique(['child_id', 'family_id']);
        });

        Schema::create("weeks", function (Blueprint $table) {
            $table->increments('id');

            $table->integer('year_id')->unsigned()->index();
            $table->foreign('year_id')->references('id')->on('years');

            $table->integer('week_number')->unsigned();

            $table->unique(['year_id', 'week_number']);

            $table->date("first_day_of_week");

            $table->timestamps();
        });
        Schema::create("week_days", function (Blueprint $table) {
            $table->increments('id');

            $table->integer('days_offset');

            $table->string('name')->unique();

            $table->timestamps();
        });
        Schema::create("playground_days", function (Blueprint $table) {
            $table->increments('id');

            $table->integer('week_id')->unsigned()->index();
            $table->foreign('week_id')->references('id')->on('weeks');

            $table->integer('week_day_id')->unsigned()->index();
            $table->foreign('week_day_id')->references('id')->on('week_days');

            $table->unique(['week_id', 'week_day_id']);

            $table->timestamps();
        });
        Schema::create("family_week_registrations", function (Blueprint $table) {
            $table->increments('id');

            $table->integer('family_id')->unsigned()->index();
            $table->foreign('family_id')->references('id')->on('families');

            $table->integer('week_id')->unsigned()->index();
            $table->foreign('week_id')->references('id')->on('weeks');

            $table->integer('tariff_id')->unsigned()->index();
            $table->foreign('tariff_id')->references('id')->on('tariffs');

            $table->unique(['family_id', 'week_id']);

            $table->timestamps();
        });
        Schema::create("child_family_week_registrations", function (Blueprint $table) {
            $table->increments('id');

            $table->integer('child_id')->unsigned()->index();
            $table->foreign('child_id')->references('id')->on('children');

            $table->integer('family_id')->unsigned()->index();
            $table->foreign('family_id')->references('id')->on('families');

            $table->integer('week_id')->unsigned()->index();
            $table->foreign('week_id')->references('id')->on('weeks');

            $table->boolean('whole_week_price');

            $table->index(['child_id', 'family_id']);
            $table->foreign(['child_id', 'family_id'])
                ->references(['child_id', 'family_id'])
                ->on('child_families');

            $table->index(['family_id', 'week_id']);
            $table->foreign(['family_id', 'week_id'])
                ->references(['family_id', 'week_id'])
                ->on('family_week_registrations');

            $table->unique(['family_id', 'week_id', 'child_id'], 'child_family_week_unique');

            $table->timestamps();
        });
        Schema::create("child_family_day_registrations", function (Blueprint $table) {
            $table->increments('id');

            $table->integer('child_id')->unsigned()->index();
            $table->foreign('child_id')->references('id')->on('children');

            $table->integer('family_id')->unsigned()->index();
            $table->foreign('family_id')->references('id')->on('families');

            $table->integer('week_id')->unsigned()->index();
            $table->foreign('week_id')->references('id')->on('weeks');

            $table->integer('week_day_id')->unsigned()->index();
            $table->foreign('week_day_id')->references('id')->on('week_days');

            $table->integer('day_part_id')->unsigned()->index();
            $table->foreign('day_part_id')->references('id')->on('day_parts');

            $table->index(['child_id', 'family_id']);
            $table->foreign(['child_id', 'family_id'])
                ->references(['child_id', 'family_id'])
                ->on('child_families');

            $table->index(['week_id', 'week_day_id']);
            $table->foreign(['week_id', 'week_day_id'])
                ->references(['week_id', 'week_day_id'])
                ->on('playground_days');

            $table->index(['family_id', 'week_id', 'child_id']);
            $table->foreign(['family_id', 'week_id', 'child_id'], 'child_family_week_registration_foreign')
                ->references(['family_id', 'week_id', 'child_id'])
                ->on('child_family_week_registrations');

            // a unique index without 'family_id' would enforce that only one family can register a child for a day
            $table->unique(['child_id', 'week_id', 'week_day_id'], 'child_playground_day_unique');

            $table->timestamps();
        });
        Schema::create("child_family_day_registration_supplements", function (Blueprint $table) {
            $table->increments('id');

            $table->integer('child_family_day_registration_id')
                ->unsigned()
                ->index('child_family_day_registration_index');
            $table->foreign('child_family_day_registration_id', 'child_family_day_registration_foreign')
                ->references('id')
                ->on('child_family_day_registrations');

            $table->integer('supplement_id')->unsigned()->index();
            $table->foreign('supplement_id')->references('id')->on('supplements');

            $table->unique(
                ['child_family_day_registration_id', 'supplement_id'],
                'child_family_playground_day_supplement_unique'
            );

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
        Schema::dropIfExists("child_family_day_registration_supplements");
        Schema::dropIfExists("child_family_day_registrations");
        Schema::dropIfExists("child_family_week_registrations");
        Schema::dropIfExists("family_week_registrations");
        Schema::dropIfExists("playground_days");
        Schema::dropIfExists("week_days");
        Schema::dropIfExists("weeks");

        Schema::create('weeks', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('year_id')->unsigned()->index();
            $table->foreign('year_id')->references('id')->on('years');

            $table->integer('week_number');

            $table->timestamps();
        });

        Schema::create('days', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('week_id')->unsigned()->index();
            $table->foreign('week_id')->references('id')->on('weeks');

            $table->date('date');

            $table->timestamps();
        });

        Schema::create('week_family_registrations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('family_id')->unsigned()->index();
            $table->foreign('family_id')->references('id')->on('families');

            $table->integer('week_id')->unsigned()->index();
            $table->foreign('week_id')->references('id')->on('weeks');

            $table->integer('tariff_id')->unsigned()->index();
            $table->foreign('tariff_id')->references('id')->on('tariffs');

            $table->timestamps();
        });

        Schema::create('week_registrations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('child_family_id')->unsigned()->index();
            $table->foreign('child_family_id')->references('id')->on('child_families');

            $table->integer('week_family_registration_id')->unsigned()->index();
            $table->foreign('week_family_registration_id')->references('id')->on('week_family_registrations');

            $table->timestamps();
        });

        Schema::create('day_registrations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('day_id')->unsigned()->index();
            $table->foreign('day_id')->references('id')->on('days');

            $table->integer('week_registration_id')->unsigned()->index();
            $table->foreign('week_registration_id')->references('id')->on('week_registrations');

            $table->integer('day_part_id')->unsigned()->index();
            $table->foreign('day_part_id')->references('id')->on('day_parts');

            $table->boolean('checked_in');

            $table->timestamps();
        });

        Schema::create('day_registration_supplements', function (Blueprint $table) {
            $table->integer('day_registration_id')->unsigned()->index();
            $table->foreign('day_registration_id')->references('id')->on('day_registrations');

            $table->integer('supplement_id')->unsigned()->index();
            $table->foreign('supplement_id')->references('id')->on('supplements');

            $table->primary(['day_registration_id', 'supplement_id'], 'registration_supplement_primary');
            $table->timestamps();
        });
    }
}
