<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

abstract class InternalPage extends BasePage
{
    protected $yearId;

    public function __construct($yearId)
    {
        $this->yearId = $yearId;
    }
    protected function getRouteParams()
    {
        return ['year' => $this->yearId];
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    abstract public function url();

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
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

    public function navigateToSettingsPage(Browser $browser)
    {
        $browser->clickLink("Extra")
            ->clickLink("Instellingen")
            ->on(new InternalSettingsPage($this->yearId));
    }
}
