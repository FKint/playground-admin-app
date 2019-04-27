<?php

namespace Tests\Browser\UserJourneys;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\DuskTestCase;

class UserJourneyCreateChildrenTest extends DuskTestCase
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
    public function testCreateChild()
    {
        $normalTariff = \App\Tariff::whereAbbreviation('NRML')->firstOrFail();
        $socialTariff = \App\Tariff::whereAbbreviation('SCL')->firstOrFail();
        $ageGroup1 = \App\AgeGroup::whereAbbreviation('6-12')->firstOrFail();
        $ageGroup2 = \App\AgeGroup::whereAbbreviation('KLS')->firstOrFail();
        $family = factory(\App\Family::class)->create(['year_id' => $this->year->id, 'guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten']);
        $child = factory(\App\Child::class)->create(['year_id' => $this->year->id, 'first_name' => 'Reinoud', 'last_name' => 'Declercq']);
        factory(\App\ChildFamily::class)->create(['year_id' => $this->year->id, 'family_id' => $family->id, 'child_id' => $child->id]);
        $this->browse(function (Browser $browser) use ($child, $normalTariff, $socialTariff, $ageGroup1, $ageGroup2) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToChildrenPage()
                ->navigateToAddNewChildDialog()
                ->enterAddChildFormData('Sonja', 'Boonen', 2004, null, null)
                ->submitAddChildFormSuccessfully()
                ->assertSeeChildEntryInTable('Sonja', 'Boonen')
                ->assertSeeEditChildDialogTabFamilies()
                ->enterAddNewFamilyFormData('Erik', 'Bulcke', $socialTariff->id, 'Requires invoice', 'Call: +329878767875')
                ->submitAddNewFamilySuccessfully('Erik Bulcke')
                ->assertSeeCurrentFamily('Erik Bulcke')
                ->enterAddExistingFamilyFormData("Reinoud")
                ->selectAddExistingFamilySuggestion("Veronique Baeten")
                ->assertSeeCurrentFamily("Erik Bulcke")
                ->assertSeeCurrentFamily("Veronique Baeten");
            $newChild = \App\Child::where(['first_name' => 'Sonja', 'last_name' => 'Boonen'])->first();
            $this->assertNotNull($newChild);
            $this->assertEquals(2, $newChild->families()->count());
            $this->assertNotNull($newChild->families()->where(['guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten'])->first());
            $family2 = $newChild->families()->where(['guardian_first_name' => 'Erik', 'guardian_last_name' => 'Bulcke'])->first();
            $this->assertNotNull($family2);

            $browser->deleteCurrentFamily($family2->id)
                ->assertDontSeeCurrentFamily("Erik Bulcke");
            $this->assertNull($newChild->families()->where(['guardian_first_name' => 'Erik', 'guardian_last_name' => 'Bulcke'])->first());

            $browser->enterAddExistingFamilyFormData("Erik Bulcke")
                ->selectAddExistingFamilySuggestion("Erik Bulcke")
                ->assertSeeCurrentFamily("Erik Bulcke")
                ->assertSeeCurrentFamily("Veronique Baeten");
            $this->assertNotNull($newChild->families()->where(['guardian_first_name' => 'Erik', 'guardian_last_name' => 'Bulcke'])->first());

            $browser->navigateToEditCurrentFamilyDialog($family2->id)
                ->enterEditFamilyForm("Jonas", null, null, null, null)
                ->submitEditFamilyFormSuccessfully();
            $family2->refresh();
            $this->assertEquals('Jonas', $family2->guardian_first_name);
            $this->assertEquals('Bulcke', $family2->guardian_last_name);
        });
    }
}
