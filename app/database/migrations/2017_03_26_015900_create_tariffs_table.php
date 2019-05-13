<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('abbreviation');
            $table->decimal('week_first_child', 5, 2);
            $table->decimal('week_later_children', 5, 2);
            $table->decimal('day_first_child', 5, 2);
            $table->decimal('day_later_children', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tariffs');
    }
}
