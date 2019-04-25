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

    abstract protected function getRouteName();

    protected function getRouteParams()
    {
        return ['year' => $this->yearId];
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route($this->getRouteName(), $this->getRouteParams());
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertRouteIs($this->getRouteName(), $this->getRouteParams());
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

    public function navigateToFamiliesPage(Browser $browser)
    {
        $browser->clickLink("Voogden")
            ->on(new InternalFamiliesPage($this->yearId));
    }
}
