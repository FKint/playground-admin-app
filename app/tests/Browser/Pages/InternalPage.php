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

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route($this->getRouteName(), $this->getRouteParams(true));
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser)
    {
        $browser->assertRouteIs($this->getRouteName(), $this->getRouteParams(false));
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
        $browser->clickLink('Dashboard')
            ->on(new InternalDashboardPage($this->yearId));
    }

    public function navigateToSettingsPage(Browser $browser)
    {
        $browser->clickLink('Extra')
            ->clickLink('Instellingen')
            ->on(new InternalSettingsPage($this->yearId));
    }

    public function navigateToFamiliesPage(Browser $browser)
    {
        $browser->clickLink('Voogden')
            ->on(new InternalFamiliesPage($this->yearId));
    }

    public function navigateToChildrenPage(Browser $browser)
    {
        $browser->clickLink('Kinderen')
            ->on(new InternalChildrenPage($this->yearId));
    }

    public function navigateToActivityListsPage(Browser $browser)
    {
        $browser->clickLink('Lijsten')
            ->on(new InternalActivityListsPage($this->yearId));
    }

    public function navigateToRegistrationsPage(Browser $browser, \Illuminate\Support\Carbon $date)
    {
        $browser->clickLink('Registraties')
            ->on(new InternalRegistrationsPage($this->yearId, $date));
    }

    public function enterAdminSessionFormData(Browser $browser, $duskSelector, $responsibleName, $actualIncome, $remarks)
    {
        $browser->waitFor('@'.$duskSelector);
        $fullSelector = '[dusk="'.$duskSelector.'"] ';
        if (!is_null($responsibleName)) {
            $browser->type($fullSelector.'[dusk="responsible_name"]', $responsibleName);
        }
        if (!is_null($actualIncome)) {
            $browser->type($fullSelector.'[dusk="counted_cash"]', $actualIncome);
        }
        if (!is_null($remarks)) {
            $browser->type($fullSelector.'[dusk="remarks"]', $remarks);
        }
    }

    abstract protected function getRouteName();

    protected function getRouteParams($includeQueryParams = true)
    {
        return ['year' => $this->yearId];
    }

    protected function enterFamilyFormData(Browser $browser, $duskSelector, $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact, $socialContact, $needsInvoice, $email = null)
    {
        // TODO(fkint): try to use assertSeeIn or within if it doesn't assert page-level conditions.
        $browser->waitFor('@'.$duskSelector);
        $browser->within('@'.$duskSelector, function ($browser) use ($guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact, $socialContact, $needsInvoice, $email) {
            if (isset($guardianFirstName)) {
                $browser->type('guardian_first_name', $guardianFirstName);
            }
            if (isset($guardianLastName)) {
                $browser->type('guardian_last_name', $guardianLastName);
            }
            if (isset($tariffId)) {
                $browser->select('tariff_id', $tariffId);
            }
            if (isset($remarks)) {
                $browser->type('remarks', $remarks);
            }
            if (isset($contact)) {
                $browser->type('contact', $contact);
            }
            if (!is_null($socialContact)) {
                $browser->type('social_contact', $socialContact);
            }
            if (!is_null($needsInvoice)) {
                $browser->radio('needs_invoice', $needsInvoice ? '1' : '0');
            }
            if (!is_null($email)) {
                $browser->type('email', $email);
            }
        });
    }

    protected function enterChildFormData(Browser $browser, $duskSelector, $firstName, $lastName, $birthYear, $ageGroupId, $remarks)
    {
        $browser->waitFor('@'.$duskSelector);
        $fullSelector = '[dusk="'.$duskSelector.'"] ';
        if (isset($firstName)) {
            $browser->type($fullSelector.' [dusk="first_name"]', $firstName);
        }
        if (isset($lastName)) {
            $browser->type($fullSelector.' [dusk="last_name"]', $lastName);
        }
        if (isset($birthYear)) {
            $browser->type($fullSelector.' [dusk="birth_year"]', $birthYear);
        }
        if (isset($ageGroupId)) {
            $browser->select($fullSelector.' [dusk="age_group_id"]', $ageGroupId);
        }
        if (isset($remarks)) {
            $browser->type($fullSelector.' [dusk="remarks"]', $remarks);
        }
    }

    protected function enterActivityListFormData(Browser $browser, $duskSelector, $name, $price, $date, $showOnAttendanceForm, $showOnDashboard)
    {
        $browser->waitFor('@'.$duskSelector);
        $fullSelector = '[dusk="'.$duskSelector.'"] ';
        if (isset($name)) {
            $browser->type($fullSelector.' [dusk="name"]', $name);
        }
        if (isset($price)) {
            $browser->type($fullSelector.' [dusk="price"]', $price);
        }
        if (isset($date)) {
            // TODO(fkint): use date picker in tests
            $browser->type($fullSelector.' [dusk="date"]', $date->format('Y-m-d'));
        }
        if (isset($showOnAttendanceForm)) {
            if ($showOnAttendanceForm) {
                $browser->check($fullSelector.' [dusk="show_on_attendance_form"]');
            } else {
                $browser->uncheck($fullSelector.' [dusk="show_on_attendance_form"]');
            }
        }
        if (isset($showOnDashboard)) {
            if ($showOnDashboard) {
                $browser->check($fullSelector.' [dusk="show_on_dashboard"]');
            } else {
                $browser->uncheck($fullSelector.' [dusk="show_on_dashboard"]');
            }
        }
    }
}
