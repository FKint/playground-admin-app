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

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
    }
}
