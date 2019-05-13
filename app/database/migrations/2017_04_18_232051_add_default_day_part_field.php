<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultDayPartField extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('day_parts', function (Blueprint $table) {
            $table->boolean('default')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('day_parts', function (Blueprint $table) {
            $table->dropColumn('default');
        });
    }
}
