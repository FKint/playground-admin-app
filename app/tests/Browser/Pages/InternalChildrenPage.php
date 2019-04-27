<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\TypeaheadComponent;

class InternalChildrenPage extends InternalPage
{
    /**
     * Get the route name for the page.
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'internal.children';
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
        $browser->assertSeeLink("Nieuw kind toevoegen");
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

    public function navigateToAddNewChildDialog(Browser $browser)
    {
        $browser->clickLink("Nieuw kind toevoegen")
            ->waitFor("@new-child-modal");
    }

    public function enterAddChildFormData(Browser $browser, $firstName, $lastName, $birthYear, $ageGroupId, $remarks)
    {
        $browser->waitFor("@new-child-modal");
        // TODO(fkint): try to use assertSeeIn or within if it doesn't assert page-level conditions.
        if (isset($firstName)) {
            $browser->type('[dusk="new-child-modal"] [dusk=first_name]', $firstName);
        }
        if (isset($lastName)) {
            $browser->type('[dusk="new-child-modal"] [dusk=last_name]', $lastName);
        }
        if (isset($birthYear)) {
            $browser->type('[dusk="new-child-modal"] [dusk=birth_year]', $birthYear);
        }
        if (isset($ageGroupId)) {
            $browser->select('[dusk="new-child-modal"] [dusk=age_group_id]', $ageGroupId);
        }
        if (isset($remarks)) {
            $browser->type('[dusk="new-child-modal"] [dusk=remarks]', $remarks);
        }
    }

    public function submitAddChildFormSuccessfully(Browser $browser)
    {
        $browser->click('[dusk="new-child-modal"] [dusk=submit]')
            ->waitFor("@edit-child-modal");
    }

    public function assertSeeChildEntryInTable(Browser $browser, $firstName, $lastName)
    {
        // TODO(fkint): Use a better selector verifying that $firstName and $lastName appear together in a row.
        $browser->waitForText($firstName)
            ->assertSeeIn("@children-table", $firstName)
            ->assertSeeIn("@children-table", $lastName);
    }

    public function assertSeeEditChildDialogTabFamilies(Browser $browser)
    {
        $browser->waitFor("@edit-child-modal")
        // TODO(fkint): try to use assertSeeIn or within if it doesn't assert page-level conditions.
            ->waitForText("Huidige voogden");
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
    public function enterAddNewFamilyFormData(Browser $browser, $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact)
    {
        $this->enterFamilyFormData($browser, "link-new-family", $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact);
    }

    public function submitAddNewFamilySuccessfully(Browser $browser, $guardianFullName)
    {
        $browser->click('[dusk="link-new-family"] [dusk=submit]')
            ->waitForText($guardianFullName)
        // TODO(fkint): find a better way to wait until the request has returned and the content can be updated.
        // Ideas: use a spinning loading icon that can be used for tests as well as for improve UX.
        ;
    }

    public function assertSeeCurrentFamily(Browser $browser, $guardianFullName)
    {
        $browser->waitFor("@current-families")
            ->assertSeeIn("@current-families", $guardianFullName);
    }

    public function assertDontSeeCurrentFamily(Browser $browser, $guardianFullName)
    {
        $browser->waitFor("@current-families")
            ->assertDontSeeIn("@current-families", $guardianFullName);
    }

    public function enterAddExistingFamilyFormData(Browser $browser, $input)
    {
        $browser->within(new TypeaheadComponent('@family-search-typeahead'), function ($browser) use ($input) {
            $browser->typeQuery($input);
        });
    }

    public function selectaddExistingFamilySuggestion(Browser $browser, $guardianFullName)
    {
        $browser->within(new TypeaheadComponent('@family-search-typeahead'), function ($browser) use ($guardianFullName) {
            $browser->selectSuggestion($guardianFullName);
        })->waitUsing(5, 1, function () use ($browser, $guardianFullName) {
            return strpos($browser->text("@current-families"), $guardianFullName) !== false;
        });
    }

    public function deleteCurrentFamily(Browser $browser, $familyId)
    {
        $selector = 'button.btn-remove-family[data-family-id="' . $familyId . '"]';
        $browser->click($selector)
            ->waitUntilMissing($selector);
    }

    public function navigateToEditCurrentFamilyDialog(Browser $browser, $familyId)
    {
        $selector = 'button.btn-edit-family[data-family-id="' . $familyId . '"]';
        $browser->click($selector)
            ->waitFor("@edit-family-modal");
    }

    public function enterEditFamilyForm(Browser $browser, $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact)
    {
        $this->enterFamilyFormData($browser, "edit-family-form", $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact);
    }

    public function submitEditFamilyFormSuccessfully(Browser $browser)
    {
        $browser->click('[dusk="edit-family-form"] [dusk=submit]')
            ->waitForText("Wijzigingen opgeslagen.");
    }
}
