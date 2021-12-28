<?php

namespace Tests\Browser\Pages;

use Illuminate\Support\Carbon;
use Laravel\Dusk\Browser;

class InternalTransactionHistoryPage extends InternalPage
{
    private $dateParam;

    public function __construct($yearId, $dateParam = null)
    {
        parent::__construct($yearId);
        $this->dateParam = $dateParam;
    }

    /**
     * Get the route name for the page.
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'internal.show_transactions_for_date';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser)
    {
        parent::assert($browser);
        $date_str = ($this->dateParam ? $this->dateParam : Carbon::now())->format('d-m-Y');
        $browser->assertSee('Transacties voor '.$date_str);
    }

    public function navigateToPreviousDate(Browser $browser, Carbon $newDate)
    {
        $browser->click('@navigate-to-previous-day')
            ->on(new InternalTransactionHistoryPage($this->yearId, $newDate));
    }

    protected function getRouteParams($includeQueryParams = true)
    {
        $params = parent::getRouteParams($includeQueryParams);
        if ($this->dateParam) {
            $params['date'] = $this->dateParam->format('Y-m-d');
        }

        return $params;
    }
}
