<?php

namespace Tests;

trait ReplaceSqliteDropForeignWithNoop
{
    /**
     * Before Laravel 5.7, dropping a foreign key in sqlite (which is not supported) silently failed.
     * Starting with Laravel 5.7, this failure breaks the whole migration.
     * This fix replaces the Blueprint for an SQlite connection such that dropForeign is a noop.
     * Source: https://github.com/laravel/framework/issues/25475#issuecomment-439342648.
     */
    protected function hotfixSqlite()
    {
        \Illuminate\Database\Connection::resolverFor('sqlite', function ($connection, $database, $prefix, $config) {
            return new class($connection, $database, $prefix, $config) extends \Illuminate\Database\SQLiteConnection {
                public function getSchemaBuilder()
                {
                    if (null === $this->schemaGrammar) {
                        $this->useDefaultSchemaGrammar();
                    }

                    return new class($this) extends \Illuminate\Database\Schema\SQLiteBuilder {
                        protected function createBlueprint($table, ?\Closure $callback = null)
                        {
                            return new class($table, $callback) extends \Illuminate\Database\Schema\Blueprint {
                                public function dropForeign($index)
                                {
                                    return new \Illuminate\Support\Fluent();
                                }
                            };
                        }
                    };
                }
            };
        });
    }
}
