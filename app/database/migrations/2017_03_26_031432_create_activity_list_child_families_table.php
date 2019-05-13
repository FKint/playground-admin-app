<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityListChildFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('activity_list_child_families', function (Blueprint $table) {
            $table->integer('child_family_id')->unsigned()->index();
            $table->integer('activity_list_id')->unsigned()->index();
            $table->primary(['child_family_id', 'activity_list_id'], 'child_family_list_primary');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('activity_list_child_families');
    }
}
