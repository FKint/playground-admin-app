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

    public function navigateToDashboardPage(Browser $browser)
    {
        $browser->clickLink("Dashboard")
            ->on(new InternalDashboardPage($this->yearId));
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

    public function navigateToActivityListsPage(Browser $browser)
    {
        $browser->clickLink("Lijsten")
            ->on(new InternalActivityListsPage($this->yearId));
    }

    public function navigateToRegistrationsPage(Browser $browser, \DateTimeImmutable $date)
    {
        $browser->clickLink("Registraties")
            ->on(new InternalRegistrationsPage($this->yearId, $date));
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

    protected function enterChildFormData(Browser $browser, $duskSelector, $firstName, $lastName, $birthYear, $ageGroupId, $remarks)
    {
        $browser->waitFor("@" . $duskSelector);
        $fullSelector = '[dusk="' . $duskSelector . '"] ';
        if (isset($firstName)) {
            $browser->type($fullSelector . ' [dusk="first_name"]', $firstName);
        }
        if (isset($lastName)) {
            $browser->type($fullSelector . ' [dusk="last_name"]', $lastName);
        }
        if (isset($birthYear)) {
            $browser->type($fullSelector . ' [dusk="birth_year"]', $birthYear);
        }
        if (isset($ageGroupId)) {
            $browser->select($fullSelector . ' [dusk="age_group_id"]', $ageGroupId);
        }
        if (isset($remarks)) {
            $browser->type($fullSelector . ' [dusk="remarks"]', $remarks);
        }
    }

    protected function enterActivityListFormData(Browser $browser, $duskSelector, $name, $price, $date, $showOnAttendanceForm, $showOnDashboard)
    {
        $browser->waitFor("@" . $duskSelector);
        $fullSelector = '[dusk="' . $duskSelector . '"] ';
        if (isset($name)) {
            $browser->type($fullSelector . ' [dusk="name"]', $name);
        }
        if (isset($price)) {
            $browser->type($fullSelector . ' [dusk="price"]', $price);
        }
        if (isset($date)) {
            // TODO(fkint): use date picker in tests
            $browser->type($fullSelector . ' [dusk="date"]', $date->format('Y-m-d'));
        }
        if (isset($showOnAttendanceForm)) {
            if ($showOnAttendanceForm) {
                $browser->check($fullSelector . ' [dusk="show_on_attendance_form"]');
            } else {
                $browser->uncheck($fullSelector . ' [dusk="show_on_attendance_form"]');
            }
        }
        if (isset($showOnDashboard)) {
            if ($showOnDashboard) {
                $browser->check($fullSelector . ' [dusk="show_on_dashboard"]');
            } else {
                $browser->uncheck($fullSelector . ' [dusk="show_on_dashboard"]');
            }
        }
    }
}
