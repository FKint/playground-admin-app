<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalEditFamilyRegistrationPage extends InternalPage
{
    protected $weekId;
    protected $familyId;
    protected $today;

    public function __construct($yearId, $weekId, $familyId, \Illuminate\Support\Carbon $today = null)
    {
        parent::__construct($yearId);
        $this->weekId = $weekId;
        $this->familyId = $familyId;
        $this->today = $today;
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->waitForText('Wijzig registratie voor familie '.$this->familyId);
        $this->waitUntilRequestsSettled($browser);
    }

    public function waitUntilRequestsSettled(Browser $browser)
    {
        $browser->waitUntilMissing('@loading-indicator');
    }

    public function assertSeeGuardianName(Browser $browser, $guardianName)
    {
        $browser->assertSee('Wijzig registratie voor familie '.$this->familyId.': '.$guardianName);
    }

    public function selectWeekRegistrationForChild(Browser $browser, $childId)
    {
        $browser->check($this->getWeekRegistrationForChildSelector($childId));
    }

    public function unselectWeekRegistrationForChild(Browser $browser, $childId)
    {
        $browser->uncheck($this->getWeekRegistrationForChildSelector($childId));
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
        $selector = 'tr[data-week-day-id="'.$weekdayId.'"] td.day-age-group[data-child-id="'.$childId.'"] select.age-group';
        $browser->select($selector, $ageGroupId);
    }

    public function selectSupplementForChild(Browser $browser, $childId, $weekdayId, $supplementId)
    {
        $selector = 'tr[data-supplement-id="'.$supplementId.'"][data-week-day-id="'.$weekdayId.'"] td.day-supplement[data-child-id="'.$childId.'"] input.registration-checkbox';
        $browser->check($selector);
    }

    public function checkInChild(Browser $browser, $childId, $weekdayId)
    {
        $browser->check($this->getChildCheckinSelector($childId, $weekdayId));
    }

    public function assertChildCheckedIn(Browser $browser, $childId, $weekdayId)
    {
        $browser->assertChecked($this->getChildCheckinSelector($childId, $weekdayId));
    }

    public function assertChildNotCheckedIn(Browser $browser, $childId, $weekdayId)
    {
        $browser->assertNotChecked($this->getChildCheckinSelector($childId, $weekdayId));
    }

    public function checkInChildrenToday(Browser $browser)
    {
        $browser->click('@btn-set-all-attending-today');
    }

    public function selectActivityListRegistrationForChild(Browser $browser, $childId, $activityListId)
    {
        $selector = 'tr[data-activity-list-id="'.$activityListId.'"] td[data-child-id="'.$childId.'"].activity-list-registration input.registration-checkbox';
        $browser->check($selector);
    }

    public function assertExpectedAmount(Browser $browser, $amount)
    {
        $browser->assertValue('@saldo_difference', $amount);
    }

    public function assertPaidFieldContent(Browser $browser, $amount)
    {
        $browser->assertValue('@received_money', $amount);
    }

    public function enterPaidField(Browser $browser, $amount)
    {
        $browser->clear('@received_money')
            ->keys('@received_money', $amount, ['{enter}', '']);
    }

    public function enterRemarksField(Browser $browser, $remarks)
    {
        $browser->type('@remarks', $remarks);
    }

    public function assertNewSaldo(Browser $browser, $amount)
    {
        $browser->assertValue('@new_saldo', $amount);
    }

    public function submitRegistrationFormAndNavigateToNext(Browser $browser)
    {
        $browser->click('@submit-registration-data-and-next')
            ->on(new InternalRegisterFindFamilyPage($this->yearId, $this->weekId));
    }

    public function submitRegistrationFormAndExpectError(Browser $browser)
    {
        $browser->click('@submit-registration-data')
            ->waitForText('Het ontvangen bedrag is niet gelijk aan het verwachte')
            ->on($this);
    }

    public function submitRegistrationFormAndNavigate(Browser $browser, $date)
    {
        $browser->click('@submit-registration-data')
            ->on(new InternalRegistrationsPage($this->yearId, $date));
    }

    public function assertSeeActivityList(Browser $browser, $name)
    {
        $browser->assertSee($name);
    }

    public function assertDontSeeActivityList(Browser $browser, $name)
    {
        $browser->assertDontSee($name);
    }

    public function navigateToTransactionHistory(Browser $browser)
    {
        $browser->clickLink('Transactiegeschiedenis')
            ->on(new InternalFamilyTransactionsPage($this->yearId, $this->familyId));
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

    protected function getRouteParams($includeQueryParams = true)
    {
        $params = parent::getRouteParams($includeQueryParams);
        $params['family'] = $this->familyId;
        $params['week'] = $this->weekId;
        if ($includeQueryParams && !is_null($this->today)) {
            $params['today'] = $this->today->format('Y-m-d');
        }

        return $params;
    }

    protected function getWeekRegistrationForChildSelector($childId)
    {
        return 'td.whole-week-registration[data-child-id="'.$childId.'"] input.registration-checkbox';
    }

    protected function getDayRegistrationForChildSelector($childId, $weekdayId)
    {
        return 'tr[data-week-day-id="'.$weekdayId.'"] td.day-registration[data-child-id="'.$childId.'"] input.registration-checkbox';
    }

    protected function getChildCheckinSelector($childId, $weekdayId)
    {
        return 'tr[data-week-day-id="'.$weekdayId.'"] td.day-attendance[data-child-id="'.$childId.'"] input.attendance-checkbox';
    }
}
