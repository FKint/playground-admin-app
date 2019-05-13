<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class InternalChildFamilyInvoicePage extends InternalPage
{
    private $familyId;
    private $childId;

    public function __construct($yearId, $familyId, $childId)
    {
        parent::__construct($yearId);
        $this->familyId = $familyId;
        $this->childId = $childId;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'internal.show_child_family_invoice_pdf';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
    }

    public function assertTotal(Browser $browser, $total)
    {
        $browser->assertSeeIn('#invoice_total', $total);
    }

    public function assertLineSubtotal(Browser $browser, $lineId, $subtotal)
    {
        $browser->assertSeeIn('tr.invoice_entry[data-iteration-id="'.$lineId.'"] .subtotal', $subtotal);
    }

    public function assertLineParticipation(Browser $browser, $lineId, $participationTotal)
    {
        $browser->assertSeeIn('tr.invoice_entry[data-iteration-id="'.$lineId.'"] .registration_price', $participationTotal);
    }

    public function assertLineSupplement(Browser $browser, $lineId, $supplementId, $supplementTotal)
    {
        $browser->assertSeeIn('tr.invoice_entry[data-iteration-id="'.$lineId.'"] .supplement_price[data-supplement-id="'.$supplementId.'"]', $supplementTotal);
    }

    public function assertLineOther(Browser $browser, $lineId, $otherTotal)
    {
        $browser->assertSeeIn('tr.invoice_entry[data-iteration-id="'.$lineId.'"] .other_price', $otherTotal);
    }

    public function assertSocialContact(Browser $browser, $socialContact)
    {
        $browser->assertSeeIn('@social_contact', $socialContact);
    }

    protected function getRouteParams($includeQueryParams = true)
    {
        $params = parent::getRouteParams($includeQueryParams);
        if ($includeQueryParams) {
            $params['html'] = true;
        }
        $params['family'] = $this->familyId;
        $params['child'] = $this->childId;

        return $params;
    }
}
