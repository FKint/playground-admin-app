<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalFamiliesPage extends InternalPage
{
    /**
     * Get the route name for the page.
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'internal.families';
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
        $browser->assertSeeLink("Nieuwe voogd toevoegen");
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

    public function navigateToAddFamilyPage(Browser $browser)
    {
        $browser->clickLink("Nieuwe voogd toevoegen")->on(new InternalAddFamilyPage($this->yearId));
    }
}
