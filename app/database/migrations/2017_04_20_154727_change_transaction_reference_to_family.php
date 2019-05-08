<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->integer('family_id')->unsigned()->default(1);
            $table->foreign('family_id')->references('id')->on('families');
        });
        DB::table("transactions")->orderBy('id')->chunk(100, function ($transactions) {
            foreach ($transactions as $transaction) {
                $child_family = DB::table('child_families')
                    ->whereId($transaction->child_family_id)
                    ->first();
                DB::table("transactions")->where('id', $transaction->id)
                    ->update([
                        'family_id' => $child_family->family_id
                    ]);
            }
        });
        Schema::table("transactions", function (Blueprint $table) {
            $table->integer('family_id')->unsigned()->default(1)->nullable(false)->change();
            $table->index('family_id');

            $table->dropForeign(['child_family_id']);
            $table->dropColumn('child_family_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table("transactions")->truncate();
        Schema::table("transactions", function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropColumn('family_id');
            $table->integer('child_family_id')->unsigned()->index()->default(0);
            $table->foreign('child_family_id')->references('id')->on('child_families');
        });
    }
}
