<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestrictUniqueDayPartOrderToPerYear extends Migration
{
    /**
     * Run the migrations.
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
     */
    public function down()
    {
        Schema::table('day_parts', function (Blueprint $table) {
            $table->unique(['order']);
            $table->dropUnique(['order', 'year_id']);
        });
    }
}
