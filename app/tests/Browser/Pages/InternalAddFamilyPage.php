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
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $browser->assertSee("Nieuwe voogd toevoegen");
    }

    public function enterAddFamilyFormData(Browser $browser, $firstName = null, $lastName = null,
        $tariffId = null, $remarks = null, $contact = null) {
        if (isset($firstName)) {
            $browser->type('@guardian_first_name', $firstName);
        }
        if (isset($lastName)) {
            $browser->type('@guardian_last_name', $lastName);
        }
        if (isset($tariffId)) {
            $browser->select('@tariff_id', $tariffId);
        }
        if (isset($remarks)) {
            $browser->type('@remarks', $remarks);
        }
        if (isset($contact)) {
            $browser->type('@contact', $contact);
        }
    }

    public function submitAddFamilySuccessfully(Browser $browser, $newFamilyId){
        $browser->click('@submit')->on(new InternalAddChildToFamilyPage($this->yearId, $newFamilyId));
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
}
