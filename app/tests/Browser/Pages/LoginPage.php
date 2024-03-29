<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class LoginPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/login';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
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

    /**
     * Submit the login form.
     *
     * @param mixed $emailAddress
     * @param mixed $password
     */
    public function submitLoginForm(Browser $browser, $emailAddress, $password)
    {
        $browser->type('email', $emailAddress)
            ->type('password', $password)
            ->press('Login');
    }
}
