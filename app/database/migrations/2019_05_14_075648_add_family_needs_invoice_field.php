<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFamilyNeedsInvoiceField extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('families', function (Blueprint $table) {
            $table->boolean('needs_invoice')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn('needs_invoice');
        });
    }
}
