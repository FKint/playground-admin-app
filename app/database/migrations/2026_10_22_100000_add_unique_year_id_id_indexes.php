<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The composite foreign keys (year_id, <relation>_id) -> <table>(year_id, id) added in add_organizations
 * reference a pair of columns, so the referenced table needs a unique index on that pair. MySQL 8.0 tolerated
 * a non-unique one, but MySQL 8.4 (restrict_fk_on_non_standard_key), the SQL standard and SQLite do not.
 * `id` is already unique, so (year_id, id) is trivially unique and the index never rejects existing data.
 *
 * add_organizations now creates these indexes itself, so new databases already have them. Databases that
 * migrated before that change get them here, which is why each index is only added when it is missing.
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
            if (Schema::hasIndex($tableName, ['year_id', 'id'], 'unique')) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) {
                $table->unique(['year_id', 'id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Intentionally empty: for databases created after add_organizations was changed, these indexes belong to
     * that migration and the foreign keys depend on them, so dropping them here would fail (or break the schema).
     */
    public function down()
    {
    }
}
