<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use ReplaceSqliteDropForeignWithNoop;

    protected function setUp(): void
    {
        $this->hotfixSqlite();
        parent::setUp();
    }
}
