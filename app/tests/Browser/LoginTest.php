<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{

    /**
     * A Dusk test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testExample()
    {
        $user = factory(User::class)->create(['email' => 'play2@ground.com', 'password' => Hash::make('longer-password')]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                ->type('email', $user->email)
                ->type('password', 'longer-password')
                ->press('Login')
                ->assertSee('Start');
        });
    }
}