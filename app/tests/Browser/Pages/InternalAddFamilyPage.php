<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalAddFamilyPage extends InternalPage
{
    /**
     * Get the route name for the page.
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'internal.show_new_family_with_children';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->assertSee('Nieuwe voogd toevoegen');
    }

    public function enterAddFamilyFormData(
        Browser $browser,
        $firstName = null,
        $lastName = null,
        $tariffId = null,
        $remarks = null,
        $contact = null,
        $socialContact = null,
        $needsInvoice = null,
        $email = null
    ) {
        $this->enterFamilyFormData(
            browser: $browser,
            duskSelector: 'new-family-form',
            guardianFirstName: $firstName,
            guardianLastName: $lastName,
            tariffId: $tariffId,
            remarks: $remarks,
            contact: $contact,
            socialContact: $socialContact,
            needsInvoice: $needsInvoice,
            email: $email
        );
    }

    public function submitAddFamilySuccessfully(Browser $browser, $newFamilyId)
    {
        $browser->click('@submit')->on(new InternalAddChildToFamilyPage($this->yearId, $newFamilyId));
    }
}
