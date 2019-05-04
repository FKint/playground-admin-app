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

    public function assertSeeActivityList(Browser $browser, $name)
    {
        $browser->assertSeeLink($name);
    }

    public function assertDontSeeActivityList(Browser $browser, $name)
    {
        $browser->assertDontSeeLink($name);
    }

    public function assertSeeAdminSession(Browser $browser, $adminSessionId, $responsibleName, $nbTransactions, $expectedIncome, $actualIncome, $error, $remarks)
    {
        $selector = '[dusk="admin-sessions-table"] tr[data-admin-session-id="' . $adminSessionId . '"] ';
        $browser->waitFor($selector)
            ->assertSeeIn($selector . '[data-field="responsible_name"]', $responsibleName)
            ->assertSeeIn($selector . '[data-field="nb_transactions"]', $nbTransactions)
            ->assertSeeIn($selector . '[data-field="expected_income"]', $expectedIncome)
            ->assertSeeIn($selector . '[data-field="actual_income"]', $actualIncome)
            ->assertSeeIn($selector . '[data-field="error"]', $error)
            ->assertSeeIn($selector . '[data-field="remarks"]', $remarks);
    }

    public function closeAdminSession(Browser $browser)
    {
        $browser->clickLink('Huidige kassa afsluiten')
            ->on(new InternalCloseAdminSessionPage($this->yearId));
    }
}
