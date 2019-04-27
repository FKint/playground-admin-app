<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

abstract class InternalPage extends BasePage
{
    protected $yearId;

    public function __construct($yearId)
    {
        $this->yearId = $yearId;
    }

    abstract protected function getRouteName();

    protected function getRouteParams()
    {
        return ['year' => $this->yearId];
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route($this->getRouteName(), $this->getRouteParams());
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertRouteIs($this->getRouteName(), $this->getRouteParams());
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

    public function navigateToSettingsPage(Browser $browser)
    {
        $browser->clickLink("Extra")
            ->clickLink("Instellingen")
            ->on(new InternalSettingsPage($this->yearId));
    }

    public function navigateToFamiliesPage(Browser $browser)
    {
        $browser->clickLink("Voogden")
            ->on(new InternalFamiliesPage($this->yearId));
    }

    public function navigateToChildrenPage(Browser $browser)
    {
        $browser->clickLink("Kinderen")
            ->on(new InternalChildrenPage($this->yearId));
    }

    protected function enterFamilyFormData(Browser $browser, $duskSelector, $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact)
    {
        // TODO(fkint): try to use assertSeeIn or within if it doesn't assert page-level conditions.
        $browser->waitFor("@" . $duskSelector);
        if (isset($guardianFirstName)) {
            $browser->type('[dusk="' . $duskSelector . '"] [dusk=guardian_first_name]', $guardianFirstName);
        }
        if (isset($guardianLastName)) {
            $browser->type('[dusk="' . $duskSelector . '"] [dusk=guardian_last_name]', $guardianLastName);
        }
        if (isset($tariffId)) {
            $browser->select('[dusk="' . $duskSelector . '"] [dusk=tariff_id]', $tariffId);
        }
        if (isset($remarks)) {
            $browser->type('[dusk="' . $duskSelector . '"] [dusk=remarks]', $remarks);
        }
        if (isset($contact)) {
            $browser->type('[dusk="' . $duskSelector . '"] [dusk=contact]', $contact);
        }
    }
}
