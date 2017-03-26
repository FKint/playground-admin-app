<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityListChildFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_list_child_families');
    }
}
