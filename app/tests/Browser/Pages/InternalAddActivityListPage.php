<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalAddActivityListPage extends InternalPage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'internal.show_create_new_list';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->assertSee('Nieuwe lijst maken');
    }

    public function enterAddActivityListFormData(Browser $browser, $name, $price, $date, $showOnAttendanceForm, $showOnDashboard)
    {
        $this->enterActivityListFormData($browser, 'new-list-form', $name, $price, $date, $showOnAttendanceForm, $showOnDashboard);
    }

    public function submitaddActivityListFormSuccessfully(Browser $browser)
    {
        $browser->click('@submit');
    }

    public function assertOnActivityListPage(Browser $browser, $activityListId)
    {
        $browser->on(new InternalActivityListPage($this->yearId, $activityListId));
    }
}
