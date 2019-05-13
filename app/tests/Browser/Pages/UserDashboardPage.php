<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class UserDashboardPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/internal';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
            ->assertSee('Start')
            ->assertSee('U heeft toegang tot de volgende jaargangen:');
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

    /**
     * Asserts that a link to the provided year is shown on the dashboard.
     *
     * @param Browser $browser
     * @param string  $yearName
     */
    public function assertShowsYear(Browser $browser, $yearName)
    {
        $browser->assertSeeLink($yearName);
    }

    /**
     * Asserts that a link to the provided year is not shown on th dashboard.
     *
     * @param Browser $browser
     * @param string  $yearName
     */
    public function assertDontShowYear(Browser $browser, $yearName)
    {
        $browser->assertDontSeeLink($yearName);
    }

    /**
     * Navigates to a year dashboard shown on the user dashboard.
     *
     * @param Browser $browser
     * @param string  $yearName
     */
    public function navigateToYearDashboard(Browser $browser, $yearName)
    {
        $browser->clickLink($yearName);
    }
}
