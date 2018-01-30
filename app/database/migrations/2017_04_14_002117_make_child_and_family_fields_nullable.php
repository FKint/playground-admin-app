<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeChildAndFamilyFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('children', function (Blueprint $table) {
            $table->string('remarks')->nullable()->change();
        });
        Schema::table('families', function(Blueprint $table){
            $table->string('remarks')->nullable()->change();
        });
        Schema::table('families', function(Blueprint $table){
            $table->string('contact')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('children', function (Blueprint $table) {
            $table->string('remarks')->change();
        });
        Schema::table('families', function(Blueprint $table){
            $table->string('remarks')->change();
        }); Schema::table('families', function(Blueprint $table){
            $table->string('contact')->change();
        });
    }
}
