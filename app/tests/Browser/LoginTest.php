<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    protected $user;

    public function setUp()
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
            $browser->visit('/')
                ->type('email', "play2@ground.com")
                ->type('password', 'longer-password')
                ->press('Login');
            $browser->driver->takeScreenshot('testLogin.png');
            $browser->assertSee('Start')
                ->assertSee('U heeft toegang tot de volgende jaargangen:');
        });
    }

    public function testLogin_BadPassword()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('email', "play2@ground.com")
                ->type('password', 'invalid-password')
                ->press('Login')
                ->assertSee('These credentials do not match our records');
        });
    }

    public function testLogin_InvalidUsername()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('email', "no-such-user@ground.com")
                ->type('password', 'longer-password')
                ->press('Login')
                ->assertSee('These credentials do not match our records');
        });
    }
}
