<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRemarksAndContactToText extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('children', function (Blueprint $table) {
            $table->text('remarks')->nullable()->change();
        });
        Schema::table('families', function (Blueprint $table) {
            $table->text('remarks')->nullable()->change();
        });
        Schema::table('families', function (Blueprint $table) {
            $table->text('contact')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('children', function (Blueprint $table) {
            $table->string('remarks')->nullable()->change();
        });
        Schema::table('families', function (Blueprint $table) {
            $table->string('remarks')->nullable()->change();
        });
        Schema::table('families', function (Blueprint $table) {
            $table->string('contact')->nullable()->change();
        });
    }
}
