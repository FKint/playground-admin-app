<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdminSessionRemarksText extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('admin_sessions', function (Blueprint $table) {
            $table->text('remarks')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('admin_sessions', function (Blueprint $table) {
            $table->string('remarks')->nullable()->change();
        });
    }
}
