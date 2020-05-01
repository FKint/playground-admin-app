<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class DatePickerComponent extends BaseComponent
{
    protected $selector;

    public function __construct($selector)
    {
        $this->selector = $selector;
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return $this->selector;
    }

    /**
     * Assert that the browser page contains the component.
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());
    }

    public function selectDate(Browser $browser, \Illuminate\Support\Carbon $date)
    {
        $browser->clear('input')
            ->keys('input', $date->format('Y-m-d'), ['{enter}', ''])
            ->pause(500);
    }
}
