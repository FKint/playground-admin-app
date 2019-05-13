<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueChildPlaygroundDay extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('child_family_day_registrations', function (Blueprint $table) {
            $table->dropUnique('child_playground_day_unique');
            $table->unique(['child_id', 'week_id', 'week_day_id', 'family_id'], 'child_family_playground_day_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('child_family_day_registrations', function (Blueprint $table) {
            $table->dropUnique('child_family_playground_day_unique');
            $table->unique(['child_id', 'week_id', 'week_day_id'], 'child_playground_day_unique');
        });
    }
}
