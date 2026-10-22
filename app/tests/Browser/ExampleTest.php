<?php

namespace Tests\Browser;

use PHPUnit\Framework\Attributes\CoversNothing;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @internal
 */
#[CoversNothing]
class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @throws \Throwable
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Log in');
        });
    }
}
