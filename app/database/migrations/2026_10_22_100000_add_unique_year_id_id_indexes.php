<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The composite foreign keys (year_id, <relation>_id) -> <table>(year_id, id) added in add_organizations
 * reference a pair of columns that has no unique index (only the `id` primary key is unique).
 * MySQL/InnoDB tolerates that, but the SQL standard and SQLite require the referenced columns to be unique.
 * `id` is already unique, so (year_id, id) is trivially unique and this index never rejects existing data.
 */
class AddUniqueYearIdIdIndexes extends Migration
{
    /**
     * The tables that are the target of a composite (year_id, id) foreign key.
     *
     * @var string[]
     */
    private const TABLES = [
        'activity_lists',
        'admin_sessions',
        'age_groups',
        'child_families',
        'child_family_day_registrations',
        'children',
        'day_parts',
        'families',
        'supplements',
        'tariffs',
        'week_days',
        'weeks',
    ];

    /**
     * Run the migrations.
     */
    public function up()
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unique(['year_id', 'id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropUnique(['year_id', 'id']);
            });
        }
    }
}
