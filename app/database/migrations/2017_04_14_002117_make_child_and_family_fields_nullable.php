<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeChildAndFamilyFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
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

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('children', function (Blueprint $table) {
            $table->string('remarks')->change();
        });
        Schema::table('families', function (Blueprint $table) {
            $table->string('remarks')->change();
        });
        Schema::table('families', function (Blueprint $table) {
            $table->string('contact')->change();
        });
    }
}
