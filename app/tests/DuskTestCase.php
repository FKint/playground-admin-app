<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Laravel\Dusk\Concerns\ProvidesBrowser;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;
    use ProvidesBrowser;
    use ReplaceSqliteDropForeignWithNoop;

    protected function setUp(): void
    {
        $this->hotfixSqlite();
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        // Close browser to remove session
        static::closeAll();
    }

    /**
     * Same as the DatabaseMigrations trait, but without rolling back after every test: each test starts with
     * migrate:fresh anyway, and rolling back runs our historical down() migrations, which SQLite (without
     * Doctrine DBAL since Laravel 11) cannot always execute, such as dropping a column that is still indexed.
     */
    public function runDatabaseMigrations()
    {
        $this->beforeRefreshingDatabase();
        $this->refreshTestDatabase();
        $this->afterRefreshingDatabase();

        $this->beforeApplicationDestroyed(function () {
            RefreshDatabaseState::$migrated = false;
        });
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions())->addArguments([
            '--disable-gpu',
            '--headless',
            '--window-size=1920,1080',
            '--verbose',
            '--dns-prefetch-disable',
            '--no-proxy-server',
            '--no-sandbox',
        ]);

        return RemoteWebDriver::create(
            'http://selenium:4444/wd/hub',
            DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options)
        );
    }
}
