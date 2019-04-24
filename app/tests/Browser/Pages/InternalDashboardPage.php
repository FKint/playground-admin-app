<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class InternalDashboardPage extends BasePage
{
    private $yearId;
    private $dateParam;

    public function __construct($yearId, $dateParam = null)
    {
        $this->yearId = $yearId;
        $this->dateParam = $dateParam;
    }

    private function getRouteParams()
    {
        $params = ['year' => $this->yearId];
        if ($this->dateParam) {
            $params['date'] = $this->dateParam->format('Y-m-d');
        }
        return $params;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('internal.dashboard', $this->getRouteParams());
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertRouteIs('internal.dashboard', $this->getRouteParams())
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
