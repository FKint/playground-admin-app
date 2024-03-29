<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFamilyEmail extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('families', function (Blueprint $table) {
            $table->string('email', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}
