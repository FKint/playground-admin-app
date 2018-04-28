<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestrictUniqueWeekDayNamesToPerYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `week_days` ADD UNIQUE( `days_offset`, `year_id`);");
        Schema::table('week_days', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `week_days` DROP INDEX `days_offset`;");
        Schema::table('week_days', function (Blueprint $table) {
            $table->unique(['name']);
        });
    }
}
