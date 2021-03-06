<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivityListChildFamilyId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('activity_list_child_families');
        Schema::create('activity_list_child_families', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('child_family_id')->unsigned()->index();
            $table->integer('activity_list_id')->unsigned()->index();
            $table->foreign('activity_list_id')->references('id')->on('activity_lists');
            $table->foreign('child_family_id')->references('id')->on('child_families');
            $table->unique(['child_family_id', 'activity_list_id'], 'child_family_list_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('activity_list_child_families');
        Schema::create('activity_list_child_families', function (Blueprint $table) {
            $table->integer('child_family_id')->unsigned()->index();
            $table->integer('activity_list_id')->unsigned()->index();
            $table->foreign('activity_list_id')->references('id')->on('activity_lists');
            $table->foreign('child_family_id')->references('id')->on('child_families');
            $table->primary(['child_family_id', 'activity_list_id'], 'child_family_list_primary');
            $table->timestamps();
        });
    }
}
