<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drops the indexes that the unique (year_id, id) indexes made redundant.
 *
 * Every table that is the target of a composite (year_id, id) foreign key now has a unique index on
 * (year_id, id), which also serves any lookup on year_id alone (it is a left prefix) and the plain
 * year_id foreign key. That makes two kinds of index obsolete:
 *  - the single-column `<table>_year_id_index` on those tables;
 *  - the non-unique `<table>_year_id_id_index` that databases created before the unique indexes existed still
 *    have on four of them (new databases never get it).
 *
 * An index is only dropped when the covering unique index is present, so this is safe to run in any state.
 */
class DropObsoleteYearIdIndexes extends Migration
{
    /**
     * The tables that have a unique (year_id, id) index (see add_unique_year_id_id_indexes).
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
            if (!Schema::hasIndex($tableName, ['year_id', 'id'], 'unique')) {
                continue;
            }
            foreach (["{$tableName}_year_id_index", "{$tableName}_year_id_id_index"] as $indexName) {
                if (Schema::hasIndex($tableName, $indexName)) {
                    Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                        $table->dropIndex($indexName);
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * Only the single-column year_id indexes are restored; the non-unique (year_id, id) duplicates are not.
     */
    public function down()
    {
        foreach (self::TABLES as $tableName) {
            if (!Schema::hasIndex($tableName, "{$tableName}_year_id_index")) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->index('year_id');
                });
            }
        }
    }
}
