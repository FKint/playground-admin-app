<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTransactionReferenceToFamily extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("transactions", function (Blueprint $table) {
            $table->dropForeign(['child_family_id']);
            $table->dropColumn('child_family_id');
            $table->integer('family_id')->unsigned()->index();
            $table->foreign('family_id')->references('id')->on('families');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("transactions", function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropColumn('family_id');
            $table->integer('child_family_id')->unsigned()->index();
            $table->foreign('child_family_id')->references('id')->on('child_families');
        });
    }
}
