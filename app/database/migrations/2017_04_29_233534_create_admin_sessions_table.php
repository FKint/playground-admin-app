<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('responsible_name')->nullable();
            $table->decimal('counted_cash')->nullable();
            $table->dateTime('session_end')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->unique(['session_end']);
        });
        DB::table('transactions')->truncate();
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('admin_session_id')->unsigned()->default(1)->index();
            $table->foreign('admin_session_id')->references('id')->on('admin_sessions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['admin_session_id']);
            $table->dropColumn('admin_session_id');
        });
        Schema::dropIfExists('admin_sessions');
    }
}
