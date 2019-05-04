<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalEditFamilyRegistrationPage extends InternalPage
{
    protected $weekId;
    protected $familyId;

    public function __construct($yearId, $weekId, $familyId)
    {
        parent::__construct($yearId);
        $this->weekId = $weekId;
        $this->familyId = $familyId;
    }
    /**
     * Get the route name for the page.
     *
     * @return string
     */
    protected function getRouteName()
    {
        return 'internal.show_edit_registration';
    }

    protected function getRouteParams()
    {
        $params = parent::getRouteParams();
        $params['family'] = $this->familyId;
        $params['week'] = $this->weekId;
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
        $browser->assertSee("Wijzig registratie voor familie " . $this->familyId);
    }

    public function assertSeeGuardianName(Browser $browser, $guardianName)
    {
        $browser->assertSee("Wijzig registratie voor familie " . $this->familyId . ": " . $guardianName);
    }

    protected function getWeekRegistrationForChildSelector($childId)
    {
        return 'td.whole-week-registration[data-child-id="' . $childId . '"] input.registration-checkbox';
    }

    public function selectWeekRegistrationForChild(Browser $browser, $childId)
    {
        $browser->check($this->getWeekRegistrationForChildSelector($childId));
    }
    
    public function unselectWeekRegistrationForChild(Browser $browser, $childId)
    {
        $browser->uncheck($this->getWeekRegistrationForChildSelector($childId));
    }

    protected function getDayRegistrationForChildSelector($childId, $weekdayId)
    {
        return 'tr[data-week-day-id="' . $weekdayId . '"] td.day-registration[data-child-id="' . $childId . '"] input.registration-checkbox';
    }

    public function selectDayRegistrationForChild(Browser $browser, $childId, $weekdayId)
    {
        $browser->check($this->getDayRegistrationForChildSelector($childId, $weekdayId));
    }

    public function unselectDayRegistrationForChild(Browser $browser, $childId, $weekdayId)
    {
        $browser->uncheck($this->getDayRegistrationForChildSelector($childId, $weekdayId));
    }

    public function assertDayRegistrationForChild(Browser $browser, $childId, $weekdayId)
    {
        $browser->assertChecked($this->getDayRegistrationForChildSelector($childId, $weekdayId));
    }

    public function assertNotDayRegistrationForChild(Browser $browser, $childId, $weekdayId)
    {
        $browser->assertNotChecked($this->getDayRegistrationForChildSelector($childId, $weekdayId));
    }

    public function selectDayAgeGroupForChild(Browser $browser, $childId, $weekdayId, $ageGroupId)
    {
        $selector = 'tr[data-week-day-id="' . $weekdayId . '"] td.day-age-group[data-child-id="' . $childId . '"] select.age-group';
        $browser->select($selector, $ageGroupId);
    }

    public function selectSupplementForChild(Browser $browser, $childId, $weekdayId, $supplementId)
    {
        $selector = 'tr[data-supplement-id="' . $supplementId . '"][data-week-day-id="' . $weekdayId . '"] td.day-supplement[data-child-id="' . $childId . '"] input.registration-checkbox';
        $browser->check($selector);
    }

    public function checkInChild(Browser $browser, $childId, $weekdayId)
    {
        $selector = 'tr[data-week-day-id="' . $weekdayId . '"] td.day-attendance[data-child-id="' . $childId . '"] input.attendance-checkbox';
        $browser->check($selector);
    }

    public function selectActivityListRegistrationForChild(Browser $browser, $childId, $activityListId)
    {
        $selector = 'tr[data-activity-list-id="' . $activityListId . '"] td[data-child-id="' . $childId . '"].activity-list-registration input.registration-checkbox';
        $browser->check($selector);
    }

    public function assertExpectedAmount(Browser $browser, $amount)
    {
        $browser->assertValue("@saldo_difference", $amount);
    }

    public function assertPaidFieldContent(Browser $browser, $amount)
    {
        $browser->assertValue("@received_money", $amount);
    }

    public function enterPaidField(Browser $browser, $amount)
    {
        $browser->clear('@received_money')
            ->keys("@received_money", $amount, ['{enter}', '']);
    }

    public function assertNewSaldo(Browser $browser, $amount)
    {
        $browser->assertValue("@new_saldo", $amount);
    }

    public function submitRegistrationFormAndNavigateToNext(Browser $browser)
    {
        $browser->click("@submit-registration-data-and-next")
            ->waitForReload()
            ->on(new InternalRegisterFindFamilyPage($this->yearId, $this->weekId));
    }

    public function assertSeeActivityList(Browser $browser, $name)
    {
        $browser->assertSee($name);
    }

    public function assertDontSeeActivityList(Browser $browser, $name)
    {
        $browser->assertDontSee($name);
    }
}
