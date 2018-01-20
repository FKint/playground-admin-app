<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUniqueChildPlaygroundDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("child_family_day_registrations", function (Blueprint $table) {
            $table->dropUnique('child_playground_day_unique');
            $table->unique(['child_id', 'week_id', 'week_day_id', 'family_id'], 'child_family_playground_day_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("child_family_day_registrations", function (Blueprint $table) {
            $table->dropUnique('child_family_playground_day_unique');
            $table->unique(['child_id', 'week_id', 'week_day_id'], 'child_playground_day_unique');
        });
    }
}
