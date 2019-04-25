<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalSettingsPage extends InternalPage
{
    /**
     * Get the route name for the page.
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'internal.settings';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->assertSee("Werkingen")
            ->assertSee("Extraatjes")
            ->assertSee("Dagdelen")
            ->assertSee("Tariefplannen");
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }
}
