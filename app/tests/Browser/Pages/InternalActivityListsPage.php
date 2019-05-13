<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalActivityListsPage extends InternalPage
{
    /**
     * Get the route name for the page.
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'internal.lists';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->assertSee('Nieuwe lijst aanmaken');
    }

    public function navigateToAddNewActivityListPage(Browser $browser)
    {
        $browser->clickLink('Nieuwe lijst aanmaken')
            ->on(new InternalAddActivityListPage($this->yearId));
    }
}
