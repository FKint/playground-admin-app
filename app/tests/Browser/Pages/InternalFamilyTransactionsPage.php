<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalFamilyTransactionsPage extends InternalPage
{
    protected $familyId;

    public function __construct($yearId, $familyId)
    {
        parent::__construct($yearId);
        $this->familyId = $familyId;
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->assertSee('Transactiegeschiedenis voor');
    }

    public function assertSaldo(Browser $browser, $saldo)
    {
        $browser->assertSee('Saldo: € '.$saldo);
    }

    /**
     * Get the route name for the page.
     *
     * @return string
     */
    protected function getRouteName()
    {
        return 'internal.show_family_transactions';
    }

    protected function getRouteParams($includeQueryParams = true)
    {
        $params = parent::getRouteParams($includeQueryParams);
        $params['family'] = $this->familyId;

        return $params;
    }
}
