<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drops the indexes that are a left prefix of another index on the same table.
 *
 * An index on (a) or (a, b) is redundant when an index on (a, b, ...) exists: the wider index serves the same
 * lookups and the same foreign keys, and the extra one only costs write performance and space. The covering
 * indexes are the unique/composite-foreign-key indexes created in update_registrations_structure,
 * add_activity_list_child_family_id and the migrations that followed.
 *
 * An index is only dropped when the covering index is present, so this is safe to run in any state.
 */
class DropIndexesCoveredByCompositeIndexes extends Migration
{
    /**
     * The redundant indexes: table => [index name => [indexed columns, columns of the index that covers it]].
     *
     * @var array
     */
    private const INDEXES = [
        'child_families' => [
            'child_families_child_id_index' => [['child_id'], ['child_id', 'family_id']],
        ],
        'playground_days' => [
            'playground_days_week_id_index' => [['week_id'], ['week_id', 'week_day_id']],
        ],
        'family_week_registrations' => [
            'family_week_registrations_family_id_index' => [['family_id'], ['family_id', 'week_id']],
        ],
        'child_family_week_registrations' => [
            'child_family_week_registrations_child_id_index' => [['child_id'], ['child_id', 'family_id']],
            'child_family_week_registrations_family_id_index' => [['family_id'], ['family_id', 'week_id', 'child_id']],
            'child_family_week_registrations_family_id_week_id_index' => [
                ['family_id', 'week_id'],
                ['family_id', 'week_id', 'child_id'],
            ],
        ],
        'child_family_day_registrations' => [
            'child_family_day_registrations_child_id_index' => [['child_id'], ['child_id', 'family_id']],
            'child_family_day_registrations_week_id_index' => [['week_id'], ['week_id', 'week_day_id']],
            'child_family_day_registrations_family_id_index' => [['family_id'], ['family_id', 'week_id', 'child_id']],
        ],
        'child_family_day_registration_supplements' => [
            'child_family_day_registration_index' => [
                ['child_family_day_registration_id'],
                ['child_family_day_registration_id', 'supplement_id'],
            ],
        ],
        'activity_list_child_families' => [
            'activity_list_child_families_child_family_id_index' => [
                ['child_family_id'],
                ['child_family_id', 'activity_list_id'],
            ],
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up()
    {
        foreach (self::INDEXES as $tableName => $indexes) {
            foreach ($indexes as $indexName => [$columns, $coveringColumns]) {
                if (Schema::hasIndex($tableName, $indexName) && Schema::hasIndex($tableName, $coveringColumns)) {
                    Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                        $table->dropIndex($indexName);
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        foreach (self::INDEXES as $tableName => $indexes) {
            foreach ($indexes as $indexName => [$columns]) {
                if (!Schema::hasIndex($tableName, $indexName)) {
                    Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                        $table->index($columns, $indexName);
                    });
                }
            }
        }
    }
}
