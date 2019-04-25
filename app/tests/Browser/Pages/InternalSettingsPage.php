<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalSettingsPage extends InternalPage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('internal.settings', $this->getRouteParams());
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertRouteIs('internal.settings', $this->getRouteParams())
            ->assertSee("Werkingen")
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
