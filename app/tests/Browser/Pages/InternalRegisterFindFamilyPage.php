<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\TypeaheadComponent;

class InternalRegisterFindFamilyPage extends InternalPage
{
    protected $weekId;

    public function __construct($yearId, $weekId)
    {
        parent::__construct($yearId);
        $this->weekId = $weekId;
    }
    /**
     * Get the route name for the page.
     *
     * @return string
     */
    protected function getRouteName()
    {
        return 'internal.show_find_family_registration';
    }

    protected function getRouteParams()
    {
        $params = parent::getRouteParams();
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
        $browser->assertSee("Registreren")
            ->assertSelected("@week", $this->weekId);
    }

    public function enterFindFamilyFormData(Browser $browser, $input)
    {
        $browser->within(new TypeaheadComponent("@family-search-typeahead"), function ($browser) use ($input) {
            $browser->typeQuery($input);
        });
    }

    public function selectFindFamilySuggestion(Browser $browser, $guardianName)
    {
        $browser->within(new TypeaheadComponent("@family-search-typeahead"), function ($browser) use ($guardianName) {
            $browser->selectSuggestion($guardianName);
        });
    }

    public function assertOnEditFamilyRegistrationPage(Browser $browser, $weekId, $familyId, $guardianName)
    {
        $browser->on(new InternalEditFamilyRegistrationPage($this->yearId, $weekId, $familyId))
            ->assertSeeGuardianName($guardianName);
    }
}
