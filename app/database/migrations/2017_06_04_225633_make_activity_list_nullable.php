<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeActivityListNullable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('activity_lists', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::table('activity_lists')->orderBy('id')->chunk(100, function ($activityLists) {
            foreach ($activityLists as $activityList) {
                if (is_null($activityList->date)) {
                    DB::table('activity_lists')->where('id', $activityList->id)
                        ->update(['date' => '2000-01-01']);
                }
            }
        });
        Schema::table('activity_lists', function (Blueprint $table) {
            $table->date('date')->nullable(false)->change();
        });
    }
}
