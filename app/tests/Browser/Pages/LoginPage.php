<?php

namespace Tests\Browser\Pages;

use Facebook\WebDriver\Exception\StaleElementReferenceException;
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
        // The page can still be settling (or re-rendering) right after the visit;
        // retry the whole interaction if an element goes stale underneath us.
        $browser->waitFor('#email')->waitFor('#password');
        for ($attempt = 1;; ++$attempt) {
            try {
                $browser->type('email', $emailAddress)
                    ->type('password', $password)
                    ->press('Login');

                return;
            } catch (StaleElementReferenceException $e) {
                if ($attempt >= 3) {
                    throw $e;
                }
                $browser->pause(200);
            }
        }
    }
}
