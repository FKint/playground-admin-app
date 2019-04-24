<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class InternalDashboardPage extends BasePage
{
    private $yearId;

    public function __construct($yearId)
    {
        $this->yearId = $yearId;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return "/internal/" . $this->yearId . "/dashboard";
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
            ->assertSee("Dashboard")
            ->assertSee("Kassa")
            ->assertSee("Registraties vandaag")
            ->assertSee("Lijsten")
            ->assertSee("Registraties overzicht");
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
