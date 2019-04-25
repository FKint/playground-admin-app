<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalDashboardPage extends InternalPage
{
    private $dateParam;

    public function __construct($yearId, $dateParam = null)
    {
        parent::__construct($yearId);
        $this->dateParam = $dateParam;
    }

    protected function getRouteParams()
    {
        $params = parent::getRouteParams();
        if ($this->dateParam) {
            $params['date'] = $this->dateParam->format('Y-m-d');
        }
        return $params;
    }

    /**
     * Get the route name for the page.
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'internal.dashboard';
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
        $browser->assertSee("Dashboard")
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
