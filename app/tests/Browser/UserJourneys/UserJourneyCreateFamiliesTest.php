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
        $normalTariff = \App\Tariff::whereAbbreviation('NRML')->firstOrFail();
        $socialTariff = \App\Tariff::whereAbbreviation('SCL')->firstOrFail();
        $ageGroup1 = \App\AgeGroup::whereAbbreviation('6-12')->firstOrFail();
        $ageGroup2 = \App\AgeGroup::whereAbbreviation('KLS')->firstOrFail();
        $this->browse(function (Browser $browser) use ($normalTariff, $socialTariff, $ageGroup1, $ageGroup2) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->navigateToAddFamilyPage()
                ->enterAddFamilyFormData('Joran', 'De Wachter', $normalTariff->id, "Only speak English", "Dad: +4987676545652")
                ->submitAddFamilySuccessfully(1)
                ->assertSeeGuardianName('Joran De Wachter')
                ->enterAddChildToFamilyFormData("Josje", "De Wachter", 2008, null, "Allergic to peanuts")
                // TODO(fkint): assert that age group 6-12 is selected
                ->submitAddChildToFamilySuccessfully()
                ->assertSeeCurrentChildName('Josje De Wachter')
                ->enterAddChildToFamilyFormData("Joris", "Janssens", 2013, null, null)
                // TODO(fkint): assert that age group KLS is selected
                ->submitAddChildToFamilySuccessfully()
                ->assertSeeCurrentChildName("Josje De Wachter")
                ->assertSeeCurrentChildName("Joris Janssens")
                ->navigateToFamiliesPage()
                ->navigateToAddFamilyPage()
                ->enterAddFamilyFormData('Annelies', 'Vandenbroucke', $socialTariff->id, "", "")
                ->submitAddFamilySuccessfully(2)
                ->assertSeeGuardianName("Annelies Vandenbroucke")
                ->enterAddChildToFamilyFormData("Rik", "Vandenbroucke", 2012, $ageGroup1->id, "")
                ->submitAddChildToFamilySuccessfully()
                ->assertSeeCurrentChildName("Rik Vandenbroucke")
                ->enterAddExistingChildFormData("De Wachter")
                ->selectAddExistingChildSuggestion("Joris Janssens")
                ->assertSeeCurrentChildName("Joris Janssens")
                ->assertSeeCurrentChildName("Rik Vandenbroucke")
                ->assertDontSeeCurrentChildName("Josje De Wachter");
            $child1 = \App\Child::where(['first_name' => "Josje", 'last_name' => "De Wachter"])->firstOrFail();
            $this->assertEquals($ageGroup1->id, $child1->age_group_id);
            $child2 = \App\Child::where(['first_name' => "Joris", 'last_name' => "Janssens"])->firstOrFail();
            $this->assertEquals($ageGroup2->id, $child2->age_group_id);
            $child3 = \App\Child::where(['first_name' => "Rik", "last_name" => "Vandenbroucke"])->firstOrFail();
            $this->assertEquals($ageGroup1->id, $child3->age_group_id);
        });
    }
}
