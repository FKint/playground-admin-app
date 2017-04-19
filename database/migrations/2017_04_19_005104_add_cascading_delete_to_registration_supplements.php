<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadingDeleteToRegistrationSupplements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
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
     *
     * @return void
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
