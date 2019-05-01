<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\TypeaheadComponent;

class InternalActivityListPage extends InternalPage
{
    private $activityListId;

    public function __construct($yearId, $activityListId)
    {
        parent::__construct($yearId);
        $this->activityListId = $activityListId;
    }
    /**
     * Get the route name for the page.
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'internal.show_list';
    }

    protected function getRouteParams()
    {
        $params = parent::getRouteParams();
        $params['list'] = $this->activityListId;
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
        $browser->assertSee("Details")
            ->assertSee("Deelnemer toevoegen")
            ->assertSee("Huidige deelnemers");
    }

    public function assertSeeActivityListDetails(Browser $browser, $name, $price, $date, $showOnAttendanceForm, $showOnDashboard)
    {
        // TODO(fkint): use within
        $browser->value("@name", $name)
            ->value("@price", $price)
            ->value("@date", $date)
            ->value("@show_on_attendance_form", $showOnAttendanceForm)
            ->value("@show_on_dashboard", $showOnDashboard);
    }

    public function assertNoActivityParticipants(Browser $browser)
    {
        $browser->assertSee("No data available in table");
    }

    public function assertActivityParticipant(Browser $browser, $participantName, $guardianName)
    {
        // TODO(fkint): improve query to make sure that the participant's name and the guardian's name appear in the same row
        $browser->assertSee($participantName)
            ->assertSee($guardianName);
    }

    public function assertNotActivityParticipant(Browser $browser, $participantName)
    {
        // TODO(fkint): improve assertion to only look in participants table
        $browser->assertDontSee($participantName);
    }

    public function enterAddParticipantFormData(Browser $browser, $input)
    {
        $browser->within(new TypeaheadComponent('@child-family-search-typeahead'), function (Browser $browser) use ($input) {
            $browser->typeQuery($input);
        });
    }

    public function selectAddParticipantSuggestion(Browser $browser, $participantName)
    {
        $browser->within(new TypeaheadComponent('@child-family-search-typeahead'), function (Browser $browser) use ($participantName) {
            $browser->selectSuggestion($participantName);
        })->waitUsing(5, 1, function () use ($browser, $participantName) {
            return strpos($browser->text('@participants-table'), $participantName) !== false;
        });
    }

    public function deleteParticipant(Browser $browser, $childFamilyId)
    {
        $selector = '.btn-remove-child-family-list[data-child-family-id="' . $childFamilyId . '"]';
        $browser->click($selector)
            ->waitUntilMissing($selector);
    }

    public function enterEditActivityListFormData(Browser $browser, $name, $price, $date, $showOnAttendanceForm, $showOnDashboard)
    {
        $this->enterActivityListFormData($browser, "edit-list-form", $name, $price, $date, $showOnAttendanceForm, $showOnDashboard);
    }
}
