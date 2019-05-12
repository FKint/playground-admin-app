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

    public function assertSeeFamilyEntryInTable(Browser $browser, $familyId, $guardianFirstName, $guardianLastName)
    {
        // TODO(fkint): Use a better selector verifying that $guardianFirstName and $guardianLastName appear together in a row.
        // Ideally make a DataTables component to re-use this for other pages
        $selector = '[dusk="families-table"] tr[data-family-id="' . $familyId . '"] ';
        $browser->waitFor($selector)
            ->assertSeeIn($selector . ' [data-field="guardian_first_name"]', $guardianFirstName)
            ->assertSeeIn($selector . ' [data-field="guardian_last_name"]', $guardianLastName);
    }
    public function assertDontSeeFamilyEntryInTable(Browser $browser, $familyId)
    {
        $selector = '[dusk="families-table"] tr[data-family-id="' . $familyId . '"] ';
        $browser->waitUntilMissing($selector);
    }

    public function navigateToEditFamily(Browser $browser, $familyId)
    {
        $selector = 'a.btn-edit-family[data-family-id="' . $familyId . '"]';
        $browser->click($selector);
    }

    public function enterEditFamilyFormData(Browser $browser, $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact)
    {
        $this->enterFamilyFormData($browser, "edit-family-form", $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact);
    }

    public function submitEditFamilyFormSuccessfully(Browser $browser)
    {
        $browser->click('[dusk="edit-family-form"] [dusk=submit]')
            ->waitForText("Wijzigingen opgeslagen.");
    }

    public function closeEditFamilyDialog(Browser $browser)
    {
        $browser->click("@btn-close-edit-family")
            ->waitUntilMissing("@edit-family-form");
    }

    public function openFamilyChildrenDialog(Browser $browser, $familyId)
    {
        $selector = 'a.btn-show-family-children[data-family-id="'.$familyId.'"]';
        $browser->waitFor($selector)
            ->click($selector)
            ->waitFor("@family-children-modal")
            ->waitFor("@family-children-table");
    }

    public function navigateToChildFamilyInvoice(Browser $browser, $familyId, $childId)
    {
        $browser->assertPresent('a.btn-child-family-invoice[data-family-id="'.$familyId.'"][data-child-id="'.$childId.'"]');
        $browser->visit(new InternalChildFamilyInvoicePage($this->yearId, $familyId, $childId));
    }
}
