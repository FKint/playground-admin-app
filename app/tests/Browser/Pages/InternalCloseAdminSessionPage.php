<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalCloseAdminSessionPage extends InternalPage
{
    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->assertSee('Kassa afsluiten');
    }

    public function enterCloseAdminSessionFormData(Browser $browser, $responsibleName, $actualIncome, $remarks)
    {
        $this->enterAdminSessionFormData($browser, 'close-admin-session-form', $responsibleName, $actualIncome, $remarks);
    }

    public function submitCloseAdminSessionFormSuccessfully(Browser $browser)
    {
        $browser->click('@submit')
            ->on(new InternalDashboardPage($this->yearId));
    }

    /**
     * Get the route name for the page.
     *
     * @return string
     */
    protected function getRouteName()
    {
        return 'internal.close_admin_session';
    }
}
