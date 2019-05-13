<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('years', function (Blueprint $table) {
            $table->text('invoice_header_text')->nullable();
            $table->binary('invoice_header_image')->nullable();
            $table->text('invoice_bank_account')->nullable();
        });
        Schema::table('families', function (Blueprint $table) {
            $table->text('social_contact')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('years', function (Blueprint $table) {
            $table->dropColumn(['invoice_header_text', 'invoice_header_image', 'invoice_bank_account']);
        });
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn('social_contact');
        });
    }
}
