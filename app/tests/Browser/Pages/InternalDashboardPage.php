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
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->assertSee('Dashboard')
            ->assertSee('Kassa')
            ->assertSee('Registraties vandaag')
            ->assertSee('Lijsten')
            ->assertSee('Registraties overzicht');
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
        $selector = '[dusk="admin-sessions-table"] tr[data-admin-session-id="'.$adminSessionId.'"] ';
        $browser->waitFor($selector);
        if (!is_null($responsibleName)) {
            $browser->assertSeeIn($selector.'[data-field="responsible_name"]', $responsibleName);
        }
        if (!is_null($nbTransactions)) {
            $browser->assertSeeIn($selector.'[data-field="nb_transactions"]', $nbTransactions);
        }
        if (!is_null($expectedIncome)) {
            $browser->assertSeeIn($selector.'[data-field="expected_income"]', $expectedIncome);
        }
        if (!is_null($actualIncome)) {
            $browser->assertSeeIn($selector.'[data-field="actual_income"]', $actualIncome);
        }
        if (!is_null($error)) {
            $browser->assertSeeIn($selector.'[data-field="error"]', $error);
        }
        if (!is_null($remarks)) {
            $browser->assertSeeIn($selector.'[data-field="remarks"]', $remarks);
        }
    }

    public function closeAdminSession(Browser $browser)
    {
        $browser->clickLink('Huidige kassa afsluiten')
            ->on(new InternalCloseAdminSessionPage($this->yearId));
    }

    public function navigateToTransactionHistory(Browser $browser)
    {
        $browser->clickLink('Transacties vandaag')
            ->on(new InternalTransactionHistoryPage($this->yearId));
    }

    protected function getRouteParams($includeQueryParams = true)
    {
        $params = parent::getRouteParams($includeQueryParams);
        if ($this->dateParam) {
            $params['date'] = $this->dateParam->format('Y-m-d');
        }

        return $params;
    }
}
