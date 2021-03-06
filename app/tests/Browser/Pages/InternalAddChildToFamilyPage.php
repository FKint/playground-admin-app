<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\TypeaheadComponent;

class InternalAddChildToFamilyPage extends InternalPage
{
    private $familyId;

    public function __construct($yearId, $familyId)
    {
        parent::__construct($yearId);
        $this->familyId = $familyId;
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->assertSee('Kind toevoegen')
            ->assertSee('Voogd:')
            ->assertSee('Huidige kinderen')
            ->assertSee('Nieuw kind toevoegen')
            ->assertSee('Bestaand kind toevoegen');
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

    public function enterAddChildToFamilyFormData(Browser $browser, $firstName = null, $lastName = null, $birthYear = null, $ageGroupId = null, $remarks = null)
    {
        // TODO(fkint): use more accurate selector for containing form
        $duskSelector = 'new-child-form';
        $this->enterChildFormData($browser, $duskSelector, $firstName, $lastName, $birthYear, $ageGroupId, $remarks);
    }

    public function submitAddChildToFamilySuccessfully(Browser $browser)
    {
        $browser->click('@submit');
    }

    public function assertSeeCurrentChildName(Browser $browser, $childName)
    {
        $browser->assertSee($childName);
        // TODO(fkint): use $browser->with(...). Currently doesn't work because it seems to check the whole page's assertions within that element.
    }

    public function assertDontSeeCurrentChildName(Browser $browser, $childName)
    {
        // TODO(fkint): use $browser->with(...). Currently doesn't work because it seems to check the whole page's assertions within that element.
        $browser->assertDontSee($childName);
    }

    public function assertSeeGuardianName(Browser $browser, $guardianName)
    {
        $browser->assertSee('Voogd: '.$guardianName);
    }

    public function enterAddExistingChildFormData(Browser $browser, $input)
    {
        $browser->within(new TypeaheadComponent('@child-search-typeahead'), function ($browser) use ($input) {
            $browser->typeQuery($input);
        });
    }

    public function selectAddExistingChildSuggestion(Browser $browser, $childName)
    {
        $browser->within(new TypeaheadComponent('@child-search-typeahead'), function ($browser) use ($childName) {
            $browser->selectSuggestion($childName)
                ->waitForReload();
        });
    }

    /**
     * Get the route name for the page.
     *
     * @return string
     */
    protected function getRouteName()
    {
        return 'internal.show_add_child_to_family';
    }

    protected function getRouteParams($includeQueryParams = true)
    {
        $params = parent::getRouteParams($includeQueryParams);
        $params['family'] = $this->familyId;

        return $params;
    }
}
