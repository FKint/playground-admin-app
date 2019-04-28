<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\DuskTestCase;

class UserJourneysTest extends DuskTestCase
{
    private $user;
    private $year;
    private $normalTariff, $socialTariff;
    private $ageGroup612, $ageGroupKls;
    private $existingFamily, $existingChild;

    public function setUp()
    {
        parent::setUp();
        app(\DatabaseSeeder::class)->call(\InitialDataSeeder::class);
        $this->year = \App\Year::firstOrFail();
        $this->user = factory(\App\User::class)->create(['organization_id' => $this->year->organization_id]);

        $this->normalTariff = \App\Tariff::whereAbbreviation('NRML')->firstOrFail();
        $this->socialTariff = \App\Tariff::whereAbbreviation('SCL')->firstOrFail();
        $this->ageGroup612 = \App\AgeGroup::whereAbbreviation('6-12')->firstOrFail();
        $this->ageGroupKls = \App\AgeGroup::whereAbbreviation('KLS')->firstOrFail();
        $this->existingFamily = factory(\App\Family::class)->create(['year_id' => $this->year->id, 'guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten']);
        $this->existingChild = factory(\App\Child::class)->create(['year_id' => $this->year->id, 'first_name' => 'Reinoud', 'last_name' => 'Declercq', 'age_group_id' => $this->ageGroupKls->id]);
        factory(\App\ChildFamily::class)->create(['year_id' => $this->year->id, 'family_id' => $this->existingFamily->id, 'child_id' => $this->existingChild->id]);
    }
    /**
     * Test for creating a new family and managing associations with new and existing children.
     *
     * @return void
     */
    public function testCreateFamily()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->navigateToAddFamilyPage()
                ->enterAddFamilyFormData('Joran', 'De Wachter', $this->normalTariff->id, "Only speak English", "Dad: +4987676545652")
            // TODO(fkint): split following call so we can use the actual Id after the entry has been inserted.
                ->submitAddFamilySuccessfully(\App\Family::count() + 1)
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
                ->enterAddFamilyFormData('Annelies', 'Vandenbroucke', $this->socialTariff->id, "", "")
                ->submitAddFamilySuccessfully(\App\Family::count() + 1)
                ->assertSeeGuardianName("Annelies Vandenbroucke")
                ->enterAddChildToFamilyFormData("Rik", "Vandenbroucke", 2012, $this->ageGroup612->id, "")
                ->submitAddChildToFamilySuccessfully()
                ->assertSeeCurrentChildName("Rik Vandenbroucke")
                ->enterAddExistingChildFormData("De Wachter")
                ->selectAddExistingChildSuggestion("Joris Janssens")
                ->assertSeeCurrentChildName("Joris Janssens")
                ->assertSeeCurrentChildName("Rik Vandenbroucke")
                ->assertDontSeeCurrentChildName("Josje De Wachter");
            $child1 = \App\Child::where(['first_name' => "Josje", 'last_name' => "De Wachter"])->firstOrFail();
            $this->assertEquals($this->ageGroup612->id, $child1->age_group_id);
            $child2 = \App\Child::where(['first_name' => "Joris", 'last_name' => "Janssens"])->firstOrFail();
            $this->assertEquals($this->ageGroupKls->id, $child2->age_group_id);
            $child3 = \App\Child::where(['first_name' => "Rik", "last_name" => "Vandenbroucke"])->firstOrFail();
            $this->assertEquals($this->ageGroup612->id, $child3->age_group_id);
        });
    }

    /**
     * Test for editing a family.
     *
     * @return void
     */
    public function testEditFamily()
    {
        $newFamily = factory(\App\Family::class)->create(['year_id' => $this->year->id, 'guardian_first_name' => 'Erica', 'guardian_last_name' => 'Van Heulen']);
        $this->browse(function (Browser $browser) use ($newFamily) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->assertSeeFamilyEntryInTable($this->existingFamily->id, "Veronique", "Baeten")
                ->assertSeeFamilyEntryInTable($newFamily->id, "Erica", "Van Heulen")
                ->navigateToEditFamily($this->existingFamily->id)
                ->enterEditFamilyFormData("Veronica", "Baetens", $this->socialTariff->id, "previously known as Veronique Baeten", "veronica@bs.com")
                ->submitEditFamilyFormSuccessfully()
                ->closeEditFamilyDialog()
                ->assertSeeFamilyEntryInTable($this->existingFamily->id, "Veronica", "Baetens")
                ->assertSeeFamilyEntryInTable($newFamily->id, "Erica", "Van Heulen");
        });
        $this->existingFamily->refresh();
        $this->assertEquals("Veronica", $this->existingFamily->guardian_first_name);
        $this->assertEquals("Baetens", $this->existingFamily->guardian_last_name);
    }

    /**
     * Test for creating a new child and managing associations with new and existing families.
     *
     * @return void
     */
    public function testCreateChild()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToChildrenPage()
                ->navigateToAddNewChildDialog()
                ->enterAddChildFormData('Sonja', 'Boonen', 2004, null, null)
                ->submitAddChildFormSuccessfully();
            $newChild = \App\Child::where(['first_name' => 'Sonja', 'last_name' => 'Boonen'])->first();
            $browser->assertSeeChildEntryInTable($newChild->id, 'Sonja', 'Boonen')
                ->assertSeeEditChildDialogTabFamilies()
                ->enterAddNewFamilyFormData('Erik', 'Bulcke', $this->socialTariff->id, 'Requires invoice', 'Call: +329878767875')
                ->submitAddNewFamilySuccessfully('Erik Bulcke')
                ->assertSeeCurrentFamily('Erik Bulcke')
                ->enterAddExistingFamilyFormData("Reinoud")
                ->selectAddExistingFamilySuggestion("Veronique Baeten")
                ->assertSeeCurrentFamily("Erik Bulcke")
                ->assertSeeCurrentFamily("Veronique Baeten");
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

    /**
     * Test for editing a child.
     *
     * @return void
     */
    public function testEditChild()
    {
        $child2 = factory(\App\Child::class)->create(['year_id' => $this->year->id, 'first_name' => 'Jan', 'last_name' => 'Cornelis']);
        $child3 = factory(\App\Child::class)->create(['year_id' => $this->year->id, 'first_name' => 'Piet', 'last_name' => 'Declercq']);

        $this->browse(function (Browser $browser) use ($child2, $child3) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToChildrenPage()
                ->assertSeeChildEntryInTable($this->existingChild->id, "Reinoud", "Declercq")
                ->assertSeeChildEntryInTable($child2->id, "Jan", "Cornelis")
                ->assertSeeChildEntryInTable($child3->id, "Piet", "Declercq")
                ->navigateToEditChildDialog($this->existingChild->id)
                ->enterEditChildFormData("Ronald", "De Clercq", 2005, null, null)
                ->submitEditChildFormSuccessfully()
                ->closeEditChildDialog()
            // TODO(fkint): add waiting period?
                ->assertSeeChildEntryInTable($this->existingChild->id, "Ronald", "De Clercq")
                ->assertSeeChildEntryInTable($child2->id, "Jan", "Cornelis")
                ->assertSeeChildEntryInTable($child3->id, "Piet", "Declercq");
        });
        $this->existingChild->refresh();
        $this->assertEquals("Ronald", $this->existingChild->first_name);
        $this->assertEquals("De Clercq", $this->existingChild->last_name);
        $this->assertEquals($this->ageGroupKls->id, $this->existingChild->age_group_id);
    }

    /**
     * Test for the settings page.
     *
     * @return void
     */
    public function testSettingsPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToSettingsPage();
        });
    }
}
