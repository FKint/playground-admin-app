<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\DatePickerComponent;

class InternalRegistrationsPage extends InternalPage
{
    protected $date;

    public function __construct($yearId, \DateTimeImmutable $date)
    {
        parent::__construct($yearId);
        $this->date = $date;
    }
    /**
     * Get the route name for the page.
     *
     * @return string
     */
    protected function getRouteName()
    {
        return 'internal.registrations_for_date';
    }

    protected function getRouteParams()
    {
        $params = parent::getRouteParams();
        $params['date'] = $this->date->format('Y-m-d');
        return $params;
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
        $browser->assertSee("Registraties");
    }

    public function navigateToRegistrationsWithDatePage(Browser $browser, \DateTimeImmutable $date)
    {
        $browser->within(new DatePickerComponent('@registration-datepicker'), function ($browser) use ($date) {
            $browser->selectDate($date);
        })->on(new InternalRegistrationsPage($this->yearId, $date));
    }

    public function navigateToRegisterFindFamilyPage(Browser $browser, $weekId)
    {
        $browser->clickLink("Registreer betalingen/aanwezigheid")
            ->on(new InternalRegisterFindFamilyPage($this->yearId, $weekId));
    }
}
