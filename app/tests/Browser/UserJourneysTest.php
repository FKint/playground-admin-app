<?php

namespace Tests\Browser;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\InitialDataSeeder;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\Browser\Pages\InternalEditFamilyRegistrationPage;
use Tests\DuskTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UserJourneysTest extends DuskTestCase
{
    private $user;
    private $year;
    private $normalTariff;
    private $socialTariff;
    private $ageGroup612;
    private $ageGroupKls;
    private $existingFamily;
    private $existingChild;
    private $existingChildFamily;

    public function setUp(): void
    {
        parent::setUp();
        app(DatabaseSeeder::class)->call(InitialDataSeeder::class);
        $this->year = \App\Year::firstOrFail();
        $this->user = \App\User::factory()->create(['organization_id' => $this->year->organization_id]);

        $this->normalTariff = \App\Tariff::whereAbbreviation('NRML')->firstOrFail();
        $this->socialTariff = \App\Tariff::whereAbbreviation('SCL')->firstOrFail();
        $this->ageGroup612 = \App\AgeGroup::whereAbbreviation('6-12')->firstOrFail();
        $this->ageGroupKls = \App\AgeGroup::whereAbbreviation('KLS')->firstOrFail();
        $this->existingFamily = \App\Family::factory()->create(['year_id' => $this->year->id, 'guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten', 'tariff_id' => $this->normalTariff->id]);
        $this->existingChild = \App\Child::factory()->create(['year_id' => $this->year->id, 'first_name' => 'Reinoud', 'last_name' => 'Declercq', 'age_group_id' => $this->ageGroupKls->id]);
        $this->existingChildFamily = \App\ChildFamily::factory()->create(['year_id' => $this->year->id, 'family_id' => $this->existingFamily->id, 'child_id' => $this->existingChild->id]);
    }

    /**
     * Test for creating a new family and managing associations with new and existing children.
     */
    public function testCreateFamily()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->navigateToAddFamilyPage()
                ->enterAddFamilyFormData(
                    firstName: 'Joran',
                    lastName: 'De Wachter',
                    tariffId: $this->normalTariff->id,
                    remarks: 'Only speak English',
                    contact: 'Dad: +4987676545652',
                    socialContact: ''
                )
            // TODO(fkint): split following call so we can use the actual Id after the entry has been inserted.
                ->submitAddFamilySuccessfully(\App\Family::count() + 1)
                ->assertSeeGuardianName('Joran De Wachter')
                ->enterAddChildToFamilyFormData('Josje', 'De Wachter', 2008, null, 'Allergic to peanuts')
            // TODO(fkint): assert that age group 6-12 is selected
                ->submitAddChildToFamilySuccessfully()
                ->assertSeeCurrentChildName('Josje De Wachter')
                ->enterAddChildToFamilyFormData('Joris', 'Janssens', 2013, null, null)
            // TODO(fkint): assert that age group KLS is selected
                ->submitAddChildToFamilySuccessfully()
                ->assertSeeCurrentChildName('Josje De Wachter')
                ->assertSeeCurrentChildName('Joris Janssens')
                ->navigateToFamiliesPage()
                ->navigateToAddFamilyPage()
                ->enterAddFamilyFormData(
                    firstName: 'Annelies',
                    lastName: 'Vandenbroucke',
                    tariffId: $this->socialTariff->id
                )
                ->submitAddFamilySuccessfully(\App\Family::count() + 1)
                ->assertSeeGuardianName('Annelies Vandenbroucke')
                ->enterAddChildToFamilyFormData('Rik', 'Vandenbroucke', 2012, $this->ageGroup612->id, '')
                ->submitAddChildToFamilySuccessfully()
                ->assertSeeCurrentChildName('Rik Vandenbroucke')
                ->enterAddExistingChildFormData('De Wachter')
                ->selectAddExistingChildSuggestion('Joris Janssens')
                ->assertSeeCurrentChildName('Joris Janssens')
                ->assertSeeCurrentChildName('Rik Vandenbroucke')
                ->assertDontSeeCurrentChildName('Josje De Wachter');
            $child1 = \App\Child::where(['first_name' => 'Josje', 'last_name' => 'De Wachter'])->firstOrFail();
            $this->assertEquals($this->ageGroup612->id, $child1->age_group_id);
            $child2 = \App\Child::where(['first_name' => 'Joris', 'last_name' => 'Janssens'])->firstOrFail();
            $this->assertEquals($this->ageGroupKls->id, $child2->age_group_id);
            $child3 = \App\Child::where(['first_name' => 'Rik', 'last_name' => 'Vandenbroucke'])->firstOrFail();
            $this->assertEquals($this->ageGroup612->id, $child3->age_group_id);
        });
    }

    /**
     * Test for editing a family.
     *
     * @group flaky
     */
    public function testEditFamily()
    {
        $newFamily = \App\Family::factory()->for($this->year)->for($this->normalTariff)->create(['guardian_first_name' => 'Erica', 'guardian_last_name' => 'Van Heulen']);
        $this->browse(function (Browser $browser) use ($newFamily) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->assertSeeFamilyEntryInTable($this->existingFamily->id, 'Veronique', 'Baeten')
                ->assertSeeFamilyEntryInTable($newFamily->id, 'Erica', 'Van Heulen')
                ->navigateToEditFamily($this->existingFamily->id)
                ->enterEditFamilyFormData(
                    guardianFirstName: 'Veronica',
                    guardianLastName: 'Baetens',
                    tariffId: $this->socialTariff->id,
                    remarks: 'previously known as Veronique Baeten',
                    contact: 'veronica@bs.com',
                    email: 'veronica@bs.com'
                )
                ->screenshot('testEditFamily_edit_family_form')
                ->submitEditFamilyFormSuccessfully()
                ->closeEditFamilyDialog()
                ->assertSeeFamilyEntryInTable($this->existingFamily->id, 'Veronica', 'Baetens')
                ->assertSeeFamilyEntryInTable($newFamily->id, 'Erica', 'Van Heulen');
        });
        $this->existingFamily->refresh();
        $this->assertEquals('Veronica', $this->existingFamily->guardian_first_name);
        $this->assertEquals('Baetens', $this->existingFamily->guardian_last_name);
    }

    /**
     * Test for creating a new child and managing associations with new and existing families.
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
            $this->assertNotNull($newChild);
            $browser->assertSeeChildEntryInTable($newChild->id, 'Sonja', 'Boonen')
                ->assertSeeEditChildDialogTabFamilies()
                ->enterAddNewFamilyFormData(
                    guardianFirstName: 'Erik',
                    guardianLastName: 'Bulcke',
                    tariffId: $this->socialTariff->id,
                    remarks: 'Requires invoice',
                    contact: 'Call: +329878767875'
                )
                ->submitAddNewFamilySuccessfully('Erik Bulcke')
                ->assertSeeCurrentFamily('Erik Bulcke')
                ->enterAddExistingFamilyFormData('Reinoud')
                ->selectAddExistingFamilySuggestion('Veronique Baeten')
                ->assertSeeCurrentFamily('Erik Bulcke')
                ->assertSeeCurrentFamily('Veronique Baeten');
            $this->assertNotNull($newChild);
            $this->assertEquals(2, $newChild->families()->count());
            $this->assertNotNull($newChild->families()->where(['guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten'])->first());
            $family2 = $newChild->families()->where(['guardian_first_name' => 'Erik', 'guardian_last_name' => 'Bulcke'])->first();
            $this->assertNotNull($family2);

            $browser->deleteCurrentFamily($family2->id)
                ->assertDontSeeCurrentFamily('Erik Bulcke');
            $this->assertNull($newChild->families()->where(['guardian_first_name' => 'Erik', 'guardian_last_name' => 'Bulcke'])->first());

            $browser->enterAddExistingFamilyFormData('Erik Bulcke')
                ->selectAddExistingFamilySuggestion('Erik Bulcke')
                ->assertSeeCurrentFamily('Erik Bulcke')
                ->assertSeeCurrentFamily('Veronique Baeten');
            $this->assertNotNull($newChild->families()->where(['guardian_first_name' => 'Erik', 'guardian_last_name' => 'Bulcke'])->first());

            $browser->navigateToEditCurrentFamilyDialog($family2->id)
                ->enterEditFamilyForm(guardianFirstName: 'Jonas')
                ->submitEditFamilyFormSuccessfully();
            $family2->refresh();
            $this->assertEquals('Jonas', $family2->guardian_first_name);
            $this->assertEquals('Bulcke', $family2->guardian_last_name);
        });
    }

    /**
     * Test for editing a child.
     */
    public function testEditChild()
    {
        $child2 = \App\Child::factory()->for($this->year)->for($this->ageGroup612)->create(['first_name' => 'Jan', 'last_name' => 'Cornelis']);
        $child3 = \App\Child::factory()->for($this->year)->for($this->ageGroup612)->create(['first_name' => 'Piet', 'last_name' => 'Declercq']);

        $this->browse(function (Browser $browser) use ($child2, $child3) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToChildrenPage()
                ->assertSeeChildEntryInTable($this->existingChild->id, 'Reinoud', 'Declercq')
                ->assertSeeChildEntryInTable($child2->id, 'Jan', 'Cornelis')
                ->assertSeeChildEntryInTable($child3->id, 'Piet', 'Declercq')
                ->navigateToEditChildDialog($this->existingChild->id)
                ->enterEditChildFormData('Ronald', 'De Clercq', 2005, null, null)
                ->submitEditChildFormSuccessfully()
                ->closeEditChildDialog()
            // TODO(fkint): add waiting period?
                ->assertSeeChildEntryInTable($this->existingChild->id, 'Ronald', 'De Clercq')
                ->assertSeeChildEntryInTable($child2->id, 'Jan', 'Cornelis')
                ->assertSeeChildEntryInTable($child3->id, 'Piet', 'Declercq');
        });
        $this->existingChild->refresh();
        $this->assertEquals('Ronald', $this->existingChild->first_name);
        $this->assertEquals('De Clercq', $this->existingChild->last_name);
        $this->assertEquals($this->ageGroupKls->id, $this->existingChild->age_group_id);
    }

    /**
     * Test for creating activity lists.
     *
     * @group flaky
     */
    public function testCreateActivityList()
    {
        $this->browse(function (Browser $browser) {
            $child2 = \App\Child::factory()->for($this->year)->for($this->ageGroup612)->create(['first_name' => 'Jan', 'last_name' => 'Cornelis']);
            $family2 = \App\Family::factory()->for($this->year)->for($this->normalTariff)->create(['guardian_first_name' => 'Arnold', 'guardian_last_name' => 'Coucke']);
            $child2->families()->syncWithoutDetaching([$family2->id => ['year_id' => $this->year->id]]);

            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToActivityListsPage()
                ->navigateToAddNewActivityListPage()
                ->enterAddActivityListFormData('Kid Rock', '3.50', new \DateTimeImmutable('2018-07-23'), true, true)
                ->submitAddActivityListFormSuccessfully();
            $activityListKidRock = \App\ActivityList::where(['name' => 'Kid Rock'])->first();
            $this->assertNotNull($activityListKidRock);
            $browser->assertOnActivityListPage($activityListKidRock->id)
                ->assertSeeActivityListDetails('Kid Rock', '3.50', '2018-07-23', true, true)
                ->assertNoActivityParticipants();

            $browser->navigateToActivityListsPage()
                ->navigateToAddNewActivityListPage()
                ->enterAddActivityListFormData('Need medication', null, null, false, true)
                ->submitAddActivityListFormSuccessfully();
            $activityListMedication = \App\ActivityList::where(['name' => 'Need medication'])->first();
            $this->assertNotNull($activityListMedication);
            $browser->assertOnActivityListPage($activityListMedication->id)
                ->assertSeeActivityListDetails('Need medication', '', '', false, true)
                ->assertNoActivityParticipants()
                ->enterAddParticipantFormData('Reinoud')
                ->selectAddParticipantSuggestion('Reinoud Declercq')
                ->assertActivityParticipant('Reinoud Declercq', 'Veronique Baeten');

            $browser->navigateToActivityListsPage()
                ->navigateToAddNewActivityListPage()
                ->enterAddActivityListFormData('Past activity', '1.00', new \DateTimeImmutable('2018-07-02'), false, false)
                ->submitAddActivityListFormSuccessfully();
            $activityListPast = \App\ActivityList::where(['name' => 'Past activity'])->first();
            $this->assertNotNull($activityListPast);
            $browser->assertOnActivityListPage($activityListPast->id)
                ->assertSeeActivityListDetails('Past activity', '1.00', '2018-07-02', false, false)
                ->enterEditActivityListFormData('Past activity: swimming', null, null, null, null)
                ->assertNoActivityParticipants()
                ->enterAddParticipantFormData('Coucke')
                ->selectAddParticipantSuggestion('Jan Cornelis')
                ->assertActivityParticipant('Jan Cornelis', 'Arnold Coucke')
                ->enterAddParticipantFormData('Reinoud')
                ->selectAddParticipantSuggestion('Reinoud Declercq')
                ->assertActivityParticipant('Reinoud Declercq', 'Veronique Baeten')
                ->deleteParticipant($this->existingChildFamily->id)
                ->assertNotActivityParticipant('Reinoud Declercq')
                ->assertActivityParticipant('Jan Cornelis', 'Arnold Coucke');

            $browser->navigateToDashboardPage()
                ->assertSeeActivityList('Need medication')
                ->assertSeeActivityList('Kid Rock')
                ->assertDontSeeActivityList('Past activity');
        });
    }

    /**
     * Test for updating registrations.
     *
     * @group flaky
     */
    public function testRegistrationsFlow()
    {
        $this->browse(function (Browser $browser) {
            $child2 = \App\Child::factory()->create(['year_id' => $this->year->id, 'first_name' => 'Jan', 'last_name' => 'Cornelis', 'age_group_id' => $this->ageGroup612->id]);
            $family2 = \App\Family::factory()->create(['year_id' => $this->year->id, 'guardian_first_name' => 'Arnold', 'guardian_last_name' => 'Coucke', 'tariff_id' => $this->normalTariff->id]);
            $child2->families()->syncWithoutDetaching([$this->existingFamily->id => ['year_id' => $this->year->id]]);
            $child2Family1 = $child2->child_families()->firstOrFail();
            $child2->families()->syncWithoutDetaching([$family2->id => ['year_id' => $this->year->id]]);
            $child2Family2 = $family2->child_families()->firstOrFail();
            $child3 = \App\Child::factory()->create(['year_id' => $this->year->id, 'first_name' => 'Wouter', 'last_name' => 'Sanders', 'age_group_id' => $this->ageGroupKls->id]);
            $child3->families()->syncWithoutDetaching([$family2->id => ['year_id' => $this->year->id]]);

            $lastDate = $this->year->playground_days()->get()->map(function ($playgroundDay) {
                return $playgroundDay->date();
            })->max();
            $date = \Illuminate\Support\Carbon::create(2018, 7, 11); // Wednesday of the second week
            $playgroundDay = $this->year->playground_days()->get()->filter(function ($playgroundDay) use ($date) {
                return $playgroundDay->date()->format('Y-m-d') === $date->format('Y-m-d');
            })->first();

            $monday = $this->year->week_days()->where('name', 'Maandag')->firstOrFail();
            $tuesday = $this->year->week_days()->where('name', 'Dinsdag')->firstOrFail();
            $wednesday = $this->year->week_days()->where('name', 'Woensdag')->firstOrFail();

            $supplementIceCream = $this->year->supplements()->where('name', 'IJsje')->firstOrFail();

            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToRegistrationsPage($lastDate)
                ->navigateToRegistrationsWithDatePage($date)
                ->navigateToRegisterFindFamilyPage($playgroundDay->week_id)
                ->enterFindFamilyFormData('Reinoud')
                ->selectFindFamilySuggestion('Reinoud Declercq')
                ->assertOnEditFamilyRegistrationPage($playgroundDay->week_id, $this->existingFamily->id, 'Veronique Baeten')
                ->selectWeekRegistrationForChild($this->existingChild->id)
                ->selectDayRegistrationForChild($child2->id, $wednesday->id)
                ->selectDayAgeGroupForChild($child2->id, $wednesday->id, $this->ageGroupKls->id)
                ->selectDayRegistrationForChild($child2->id, $monday->id)
                ->selectDayAgeGroupForChild($child2->id, $monday->id, $this->ageGroupKls->id)
                ->selectSupplementForChild($this->existingChild->id, $monday->id, $supplementIceCream->id)
                ->selectSupplementForChild($child2->id, $wednesday->id, $supplementIceCream->id)
                ->checkInChild($child2->id, $wednesday->id)
                ->waitUntilRequestsSettled()
                ->assertExpectedAmount('31.50')
                ->assertPaidFieldContent('0.00')
                ->enterPaidField('31.50')
                ->submitRegistrationFormAndNavigateToNext();

            $transaction = $this->year->getActiveAdminSession()->transactions()->where('family_id', $this->existingFamily->id)->first();
            $this->assertNotNull($transaction);
            $this->assertEquals('31.50', number_format($transaction->amount_expected, 2));
            $this->assertEquals('31.50', number_format($transaction->amount_paid, 2));

            $this->assertEquals(5, $this->existingChildFamily->child_family_day_registrations()->count());
            $this->assertEquals(2, $child2Family1->child_family_day_registrations()->count());
            $this->assertEquals(2, $child2Family1->child_family_day_registrations()->where('age_group_id', $this->ageGroupKls->id)->count());
            $wednesdayRegistrationChild2 = $child2Family1->child_family_day_registrations()->where(['week_day_id' => $wednesday->id])->first();
            $this->assertNotNull($wednesdayRegistrationChild2);
            $this->assertTrue((bool) $wednesdayRegistrationChild2->attended);
            $this->assertEquals(1, $wednesdayRegistrationChild2->supplements()->count());
            $mondayRegistrationChild2 = $child2Family1->child_family_day_registrations()->where(['week_day_id' => $monday->id])->first();
            $this->assertNotNull($mondayRegistrationChild2);
            $this->assertFalse((bool) $mondayRegistrationChild2->attended);
            $this->assertEquals(0, $mondayRegistrationChild2->supplements()->count());
            $mondayRegistrationExistingChild = $this->existingChildFamily->child_family_day_registrations()->where(['week_day_id' => $wednesday->id])->first();
            $this->assertNotNull($mondayRegistrationExistingChild);
            $this->assertFalse((bool) $mondayRegistrationExistingChild->attended);

            $activityList = \App\ActivityList::factory()->create(['year_id' => $this->year->id, 'name' => 'Kid Rock', 'show_on_attendance_form' => true, 'price' => '0.89']);
            $activityList2 = \App\ActivityList::factory()->create(['year_id' => $this->year->id, 'name' => 'Swimming', 'show_on_attendance_form' => false]);

            $browser->enterFindFamilyFormData('Wouter Sanders')
                ->selectFindFamilySuggestion('Arnold Coucke')
                ->assertOnEditFamilyRegistrationPage($playgroundDay->week_id, $family2->id, 'Arnold Coucke')
                ->assertSeeActivityList('Kid Rock')
                ->assertDontSeeActivityList('Swimming')
                ->selectWeekRegistrationForChild($child3->id)
                ->selectDayRegistrationForChild($child2->id, $tuesday->id)
                ->assertDayRegistrationForChild($child2->id, $tuesday->id)
                ->assertNotDayRegistrationForChild($child2->id, $wednesday->id)
                ->selectActivityListRegistrationForChild($child3->id, $activityList->id)
                ->checkInChild($child3->id, $tuesday->id)
                ->checkInChild($child2->id, $tuesday->id)
                ->waitUntilRequestsSettled()
                ->assertExpectedAmount('27.39')
                ->enterPaidField('25')
                ->enterRemarksField('not enough cash')
                ->assertNewSaldo('2.39')
                ->submitRegistrationFormAndNavigateToNext();

            $this->assertEquals(1, $activityList->child_families()->count());
            $participatingChildFamily = $activityList->child_families()->first();
            $this->assertEquals($child3->id, $participatingChildFamily->child_id);

            $browser->navigateToDashboardPage()
                ->closeAdminSession()
                ->enterCloseAdminSessionFormData('The Admin', '55.00', "Dropped some coins and didn't find all of them.")
                ->submitCloseAdminSessionFormSuccessfully();
            $adminSession = \App\AdminSession::where('responsible_name', 'The Admin')->first();
            $this->assertNotNull($adminSession);
            $this->assertEquals('55.00', number_format($adminSession->counted_cash, 2));
            $this->assertEquals(2, $adminSession->transactions()->count());
            $newAdminSession = $this->year->getActiveAdminSession();
            $this->assertNotNull($newAdminSession);
            $this->assertEquals(0, $newAdminSession->transactions()->count());
            $browser->assertSeeAdminSession($adminSession->id, 'The Admin', 2, '56.50', '55.00', '-1.50', "Dropped some coins and didn't find all of them.");

            $browser->visit(new InternalEditFamilyRegistrationPage($this->year->id, $playgroundDay->week_id, $this->existingFamily->id, $date))
                ->assertChildCheckedIn($child2->id, $wednesday->id)
                ->assertChildNotCheckedIn($this->existingChild->id, $wednesday->id)
                ->checkInChildrenToday()
                ->assertChildCheckedIn($child2->id, $wednesday->id)
                ->assertChildCheckedIn($this->existingChild->id, $wednesday->id)
                ->assertChildNotCheckedIn($this->existingChild->id, $monday->id)
                ->unselectWeekRegistrationForChild($this->existingChild->id)
                ->unselectDayRegistrationForChild($child2->id, $wednesday->id)
                ->waitUntilRequestsSettled()
                ->selectWeekRegistrationForChild($this->existingChild->id)
                ->selectDayRegistrationForChild($child2->id, $wednesday->id)
                ->waitUntilRequestsSettled()
                ->assertExpectedAmount('-1.00') // only supplements are cancelled
                ->unselectWeekRegistrationForChild($this->existingChild->id)
                ->unselectDayRegistrationForChild($child2->id, $wednesday->id)
                ->waitUntilRequestsSettled()
                ->assertExpectedAmount('-26.50')
                ->enterPaidField('-24.50')
                ->enterRemarksField('remarks')
                ->submitRegistrationFormAndNavigateToNext()
                ->navigateToDashboardPage()
                ->assertSeeAdminSession($newAdminSession->id, null, 1, '-24.50', null, null, null);

            $browser->navigateToRegistrationsPage($lastDate)
                ->navigateToRegistrationsWithDatePage($date)
                ->navigateToRegisterFindFamilyPage($playgroundDay->week_id)
                ->enterFindFamilyFormData('Reinoud')
                ->selectFindFamilySuggestion('Reinoud Declercq')
                ->assertOnEditFamilyRegistrationPage($playgroundDay->week_id, $this->existingFamily->id, 'Veronique Baeten')
                ->navigateToTransactionHistory()
                ->assertSaldo('-2.00');
        });
    }

    /**
     * Test for the settings page.
     */
    public function testSettingsPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToSettingsPage()
                ->screenshot('settings_page');
        });
    }
}
