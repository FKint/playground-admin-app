<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LoginPage;
use Tests\Browser\Pages\UserDashboardPage;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['email' => 'play2@ground.com', 'password' => Hash::make('longer-password')]);
    }

    /**
     * Logging in.
     *
     * @return void
     * @throws \Throwable
     */
    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            // TODO(fkint): add visual diff
            $browser->visit(new LoginPage)
                ->submitLoginForm("play2@ground.com", "longer-password")
                ->on(new UserDashboardPage);
        });
    }

    public function testLogin_BadPassword()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage)
                ->submitLoginForm("play2@ground.com", "invalid-password")
                ->assertSee('These credentials do not match our records')
                ->on(new LoginPage);
        });
    }

    public function testLogin_InvalidUsername()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage)
                ->submitLoginForm("no-such-user@ground.com", "longer-password")
                ->assertSee('These credentials do not match our records')
                ->on(new LoginPage);
        });
    }
}
