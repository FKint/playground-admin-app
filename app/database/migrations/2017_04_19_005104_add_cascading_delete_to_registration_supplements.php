<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCascadingDeleteToRegistrationSupplements extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('child_family_day_registration_supplements', function (Blueprint $table) {
            $table->dropForeign('child_family_day_registration_foreign');
            $table->foreign('child_family_day_registration_id', 'child_family_day_registration_foreign')
                ->references('id')
                ->on('child_family_day_registrations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('child_family_day_registration_supplements', function (Blueprint $table) {
            $table->dropForeign('child_family_day_registration_foreign');
            $table->foreign('child_family_day_registration_id', 'child_family_day_registration_foreign')
                ->references('id')
                ->on('child_family_day_registrations');
        });
    }
}
