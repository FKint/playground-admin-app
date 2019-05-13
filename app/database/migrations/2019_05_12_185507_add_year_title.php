<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearTitle extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('years', function (Blueprint $table) {
            $table->text('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('years', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
}
