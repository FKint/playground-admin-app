<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckedInField extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('day_registrations', function (Blueprint $table) {
            $table->boolean('checked_in')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('day_registrations', function (Blueprint $table) {
            $table->dropColumn('checked_in');
        });
    }
}
