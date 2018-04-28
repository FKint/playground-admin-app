<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestrictUniqueDayPartOrderToPerYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('day_parts', function (Blueprint $table) {
            $table->unique(['order', 'year_id']);
            $table->dropUnique(['order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('day_parts', function (Blueprint $table) {
            $table->unique(['order']);
            $table->dropUnique(['order', 'year_id']);
        });
    }
}
