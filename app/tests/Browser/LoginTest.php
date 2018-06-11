<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = factory(User::class)->create(['email' => 'play@ground.com', 'password' => 'game']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                ->type('email', $user->email)
                ->type('password', $user->password)
                ->press('Login')
                ->assertSee('Start');
        });
    }
}
