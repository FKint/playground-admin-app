<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestrictUniqueWeekDayNamesToPerYear extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('week_days', function (Blueprint $table) {
            $table->unique(['days_offset', 'year_id']);
            $table->dropUnique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('week_days', function (Blueprint $table) {
            $table->dropUnique(['days_offset', 'year_id']);
            $table->unique(['name']);
        });
    }
}
