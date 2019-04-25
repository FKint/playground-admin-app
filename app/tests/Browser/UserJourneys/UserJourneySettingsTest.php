<?php

namespace Tests\Browser\UserJourneys;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\DuskTestCase;

class UserJourneySettingsTest extends DuskTestCase
{
    private $user;
    private $year;

    public function setUp()
    {
        parent::setUp();
        app(\DatabaseSeeder::class)->call(\InitialDataSeeder::class);
        $this->year = \App\Year::firstOrFail();
        $this->user = factory(\App\User::class)->create(['organization_id' => $this->year->organization_id]);
    }
    /**
     * Verifies that the settings page can be accessed from the Internal Dashboard and that the page is displayed correctly.
     *
     * @return void
     */
    public function testShowsSections()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToSettingsPage();
        });
    }
}
