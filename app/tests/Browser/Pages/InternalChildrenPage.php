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
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->assertSeeLink('Nieuw kind toevoegen');
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
        $browser->clickLink('Nieuw kind toevoegen')
            ->waitFor('@new-child-modal')
            ->waitFor('[dusk="new-child-modal"] [dusk="first_name"]:focus');
    }

    public function enterAddChildFormData(Browser $browser, $firstName, $lastName, $birthYear, $ageGroupId, $remarks)
    {
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
            ->waitFor('@edit-child-modal');
    }

    public function assertSeeChildEntryInTable(Browser $browser, $childId, $firstName, $lastName)
    {
        $selector = '[dusk="children-table"] tr[data-child-id="'.$childId.'"] ';
        $browser->waitFor($selector)
            ->assertSeeIn($selector.'[data-field="first_name"]', $firstName)
            ->assertSeeIn($selector.'[data-field="last_name"]', $lastName);
    }

    public function assertSeeEditChildDialogTabFamilies(Browser $browser)
    {
        $browser->waitFor('@edit-child-modal')
        // TODO(fkint): try to use assertSeeIn or within if it doesn't assert page-level conditions.
            ->waitForText('Huidige voogden');
    }

    public function enterAddNewFamilyFormData(Browser $browser, $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact, $socialContact, $needsInvoice, $email)
    {
        $this->enterFamilyFormData($browser, 'link-new-family', $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact, $socialContact, $needsInvoice, $email);
    }

    public function submitAddNewFamilySuccessfully(Browser $browser, $guardianFullName)
    {
        $browser->click('[dusk="link-new-family"] [dusk=submit]')
            ->waitForText($guardianFullName);
        // TODO(fkint): find a better way to wait until the request has returned and the content can be updated.
        // Ideas: use a spinning loading icon that can be used for tests as well as for improve UX.
    }

    public function assertSeeCurrentFamily(Browser $browser, $guardianFullName)
    {
        $browser->waitFor('@current-families')
            ->assertSeeIn('@current-families', $guardianFullName);
    }

    public function assertDontSeeCurrentFamily(Browser $browser, $guardianFullName)
    {
        $browser->waitFor('@current-families')
            ->assertDontSeeIn('@current-families', $guardianFullName);
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
            return false !== strpos($browser->text('@current-families'), $guardianFullName);
        });
    }

    public function deleteCurrentFamily(Browser $browser, $familyId)
    {
        $selector = 'button.btn-remove-family[data-family-id="'.$familyId.'"]';
        $browser->click($selector)
            ->waitUntilMissing($selector);
    }

    public function navigateToEditCurrentFamilyDialog(Browser $browser, $familyId)
    {
        $selector = 'button.btn-edit-family[data-family-id="'.$familyId.'"]';
        $browser->click($selector)
            ->waitFor('@edit-family-modal');
    }

    public function enterEditFamilyForm(Browser $browser, $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact, $socialContact, $needsInvoice, $email)
    {
        $this->enterFamilyFormData($browser, 'edit-family-form', $guardianFirstName, $guardianLastName, $tariffId, $remarks, $contact, $socialContact, $needsInvoice, $email);
    }

    public function submitEditFamilyFormSuccessfully(Browser $browser)
    {
        $browser->click('[dusk="edit-family-form"] [dusk=submit]')
            ->waitForText('Wijzigingen opgeslagen.');
    }

    public function navigateToEditChildDialog(Browser $browser, $childId)
    {
        $selector = 'a.btn-edit-child[data-child-id="'.$childId.'"]';
        $browser->click($selector)
            ->waitFor('@edit-child-modal');
    }

    public function enterEditChildFormData(Browser $browser, $firstName, $lastName, $birthYear, $ageGroupId, $remarks)
    {
        $this->enterChildFormData($browser, 'edit-child-form', $firstName, $lastName, $birthYear, $ageGroupId, $remarks);
    }

    public function submitEditChildFormSuccessfully(Browser $browser)
    {
        $browser->click('[dusk="edit-child-form"] [dusk=submit]')
            ->waitForText('Wijzigingen opgeslagen');
    }

    public function closeEditChildDialog(Browser $browser)
    {
        $browser->click('@btn-close-edit-child')
            ->waitUntilMissing('@edit-child-form');
    }
}
