<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class TypeaheadComponent extends BaseComponent
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

    public function typeQuery(Browser $browser, $input)
    {
        return $browser->type('@input', $input);
    }

    public function selectSuggestion(Browser $browser, $input)
    {
        $browser->waitForLink($input)
            ->clickLink($input);
    }

    public function typeAndSelectSuggestion(Browser $browser, $input, $suggestion)
    {
        // TODO(fkint): use $browser->within
        $this->typeQuery($this->selector(), $input)
            ->clickLink($suggestion);
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@input' => 'input:not([readonly])',
        ];
    }
}
