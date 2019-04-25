<?php

namespace Tests\Browser\UserJourneys;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\DuskTestCase;

class UserJourneyCreateFamiliesTest extends DuskTestCase
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
     * A Dusk test example.
     *
     * @return void
     */
    public function testCreateFamily()
    {
        $tariff = \App\Tariff::whereAbbreviation('NRML')->firstOrFail();
        $ageGroup1 = \App\AgeGroup::whereAbbreviation('6-12')->firstOrFail();
        $ageGroup2 = \App\AgeGroup::whereAbbreviation('KLS')->firstOrFail();
        $this->browse(function (Browser $browser) use ($tariff, $ageGroup1, $ageGroup2) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->navigateToAddFamilyPage()
                ->enterAddFamilyFormData('Joran', 'De Wachter', $tariff->id, "Only speak English", "Dad: +4987676545652")
                ->submitAddFamilySuccessfully(1)
                ->assertSeeGuardianName('Joran De Wachter')
                ->enterAddChildToFamilyFormData("Josje", "De Wachter", 2008, null, "Allergic to peanuts")
                ->submitAddChildToFamilySuccessfully()
                ->assertSeeCurrentChildName('Josje De Wachter')
                ->enterAddChildToFamilyFormData("Joris", "Janssens", 2013, null, null)
                ->submitAddChildToFamilySuccessfully()
                ->assertSeeCurrentChildName("Josje De Wachter")
                ->assertSeeCurrentChildName("Joris Janssens");
            $child1 = \App\Child::where(['first_name' => "Josje", 'last_name' => "De Wachter"])->firstOrFail();
            $this->assertEquals($ageGroup1->id, $child1->age_group_id);
            $child2 = \App\Child::where(['first_name' => "Joris", 'last_name' => "Janssens"])->firstOrFail();
            $this->assertEquals($ageGroup2->id, $child2->age_group_id);
        });
    }
}
