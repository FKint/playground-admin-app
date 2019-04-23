<?php

namespace Tests\Browser;

use App\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LoginPage;
use Tests\Browser\Pages\UserDashboardPage;
use Tests\DuskTestCase;

class HomeTest extends DuskTestCase
{
    /**
     * Navigates to the login page when not logged in.
     *
     * @return void
     */
    public function testHome_NotAuthenticatedToLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->on(new LoginPage);
        });
    }

    /**
     * Navigates to the user dashboard when logged in.
     *
     * @return void
     */
    public function testHome_AuthenticatedToUserDashboardPage()
    {
        $user = factory(User::class)->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->on(new UserDashboardPage);
        });
    }
}
