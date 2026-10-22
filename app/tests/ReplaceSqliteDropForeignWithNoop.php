<?php

namespace Tests;

trait ReplaceSqliteDropForeignWithNoop
{
    /**
     * SQLite cannot drop a foreign key by name, which breaks some of our older migrations.
     * This fix replaces the Blueprint for an SQLite connection such that dropForeign by name is a noop
     * (dropping by column list is supported natively since Laravel 11).
     * Call this before the application boots, e.g. at the start of setUp().
     * Source: https://github.com/laravel/framework/issues/25475#issuecomment-439342648.
     */
    protected function hotfixSqlite()
    {
        \Illuminate\Database\Connection::resolverFor('sqlite', function ($connection, $database, $prefix, $config) {
            return new class ($connection, $database, $prefix, $config) extends \Illuminate\Database\SQLiteConnection {
                public function getSchemaBuilder()
                {
                    if (null === $this->schemaGrammar) {
                        $this->useDefaultSchemaGrammar();
                    }

                    return new class ($this) extends \Illuminate\Database\Schema\SQLiteBuilder {
                        protected function createBlueprint($table, ?\Closure $callback = null)
                        {
                            return new class ($this->connection, $table, $callback) extends \Illuminate\Database\Schema\Blueprint {
                                public function dropForeign($index)
                                {
                                    if (is_string($index)) {
                                        return new \Illuminate\Support\Fluent();
                                    }

                                    return parent::dropForeign($index);
                                }
                            };
                        }
                    };
                }
            };
        });
    }
}
