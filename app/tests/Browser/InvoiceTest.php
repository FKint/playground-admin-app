<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\DuskTestCase;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\InitialDataSeeder;

/**
 * @internal
 * @coversNothing
 */
class InvoiceTest extends DuskTestCase
{
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
    }

    /**
     * A Dusk test example.
     */
    public function testInvoices()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->navigateToAddFamilyPage()
                ->enterAddFamilyFormData('Veronique', 'Baeten', $this->normalTariff->id, '', '', 'SUP', true)
                ->submitAddFamilySuccessfully(1)
                ->enterAddChildToFamilyFormData('Reinoud', 'Declercq', 2008, null, null)
                ->submitAddChildToFamilySuccessfully()
                ->enterAddChildToFamilyFormData('Piet', 'Declercq', 2010, null, null)
                ->submitAddChildToFamilySuccessfully()
                ->navigateToFamiliesPage()
                ->navigateToAddFamilyPage()
                ->enterAddFamilyFormData('Erica', 'Van Heulen', $this->socialTariff->id, '', '', '', true)
                ->submitAddFamilySuccessfully(2)
                ->enterAddChildToFamilyFormData('Jan', 'Cornelis', 2012, null, null)
                ->submitAddChildToFamilySuccessfully()
                ->enterAddExistingChildFormData('Declercq')
                ->selectAddExistingChildSuggestion('Piet Declercq');

            // set up 2 families A, B
            $familyVeroniqueBaeten = \App\Family::where(['guardian_first_name' => 'Veronique'])->firstOrFail();
            $familyEricaVanHeulen = \App\Family::where(['guardian_first_name' => 'Erica'])->firstOrFail();
            // x,y,z children
            $childReinoudDeclercq = \App\Child::where(['first_name' => 'Reinoud'])->firstOrFail();
            $childPietDeclercq = \App\Child::where(['first_name' => 'Piet'])->firstOrFail();
            $childJanCornelis = \App\Child::where(['first_name' => 'Jan'])->firstOrFail();
            // child families Ax, Ay, By, Bz

            // activity 1,2 subscriptions Ax1 (during week signup), Ay1 (during day signup), Ay2 (no date)
            $browser->navigateToActivityListsPage()
                ->navigateToAddNewActivityListPage()
                ->enterAddActivityListFormData('Kid Rock', '0.89', \Illuminate\Support\Carbon::create(2018, 7, 23), true, true)
                ->submitAddActivityListFormSuccessfully();
            $activityListWithDate = \App\ActivityList::where(['name' => 'Kid Rock'])->firstOrFail();
            $browser->assertOnActivityListPage($activityListWithDate->id)
                ->navigateToActivityListsPage()
                ->navigateToAddNewActivityListPage()
                ->enterAddActivityListFormData('T-shirt', '5.21', null, true, true)
                ->submitAddActivityListFormSuccessfully();
            $activityListNoDate = \App\ActivityList::where(['name' => 'T-shirt'])->firstOrFail();
            $browser->assertOnActivityListPage($activityListNoDate->id);

            $lastDate = $this->year->playground_days()->get()->map(function ($playgroundDay) {
                return $playgroundDay->date();
            })->max();
            $dateKidRock = \Illuminate\Support\Carbon::create(2018, 7, 23);
            $playgroundDayKidRock = $this->year->playground_days()->get()->filter(function ($playgroundDay) use ($dateKidRock) {
                return $playgroundDay->date()->isSameDay($dateKidRock);
            })->first();
            $monday = $this->year->week_days()->where('name', 'Maandag')->firstOrFail();
            $tuesday = $this->year->week_days()->where('name', 'Dinsdag')->firstOrFail();
            $wednesday = $this->year->week_days()->where('name', 'Woensdag')->firstOrFail();
            $supplementIceCream = $this->year->supplements()->where('name', 'IJsje')->firstOrFail();

            $browser->navigateToRegistrationsPage($lastDate)
                ->navigateToRegistrationsWithDatePage($dateKidRock)
                ->navigateToRegisterFindFamilyPage($playgroundDayKidRock->week_id)
                ->enterFindFamilyFormData('Reinoud Declercq')
                ->selectFindFamilySuggestion('Veronique')
                ->assertOnEditFamilyRegistrationPage($playgroundDayKidRock->week_id, $familyVeroniqueBaeten->id, 'Veronique Baeten')
                ->selectWeekRegistrationForChild($childReinoudDeclercq->id)
                ->selectDayRegistrationForChild($childPietDeclercq->id, $wednesday->id)
                ->selectSupplementForChild($childPietDeclercq->id, $wednesday->id, $supplementIceCream->id)
                ->selectSupplementForChild($childReinoudDeclercq->id, $tuesday->id, $supplementIceCream->id)
                ->selectActivityListRegistrationForChild($childReinoudDeclercq->id, $activityListWithDate->id)
                ->waitUntilRequestsSettled()
                ->assertPaidFieldContent('0.00')
                ->screenshot('invoices_registration_week_with_activity')
                ->submitRegistrationFormAndNavigateToNext();

            $dateWeek1 = \Illuminate\Support\Carbon::create(2018, 7, 3);
            $playgroundDayWeek1 = $this->year->playground_days()->get()->filter(function ($playgroundDay) use ($dateWeek1) {
                return $playgroundDay->date()->isSameDay($dateWeek1);
            })->first();
            $browser->navigateToRegistrationsPage($lastDate)
                ->navigateToRegistrationsWithDatePage($dateWeek1)
                ->navigateToRegisterFindFamilyPage($playgroundDayWeek1->week_id)
                ->enterFindFamilyFormData('Reinoud Declercq')
                ->selectFindFamilySuggestion('Veronique')
                ->assertOnEditFamilyRegistrationPage($playgroundDayWeek1->week_id, $familyVeroniqueBaeten->id, 'Veronique Baeten')
                ->selectWeekRegistrationForChild($childPietDeclercq->id)
                ->selectDayRegistrationForChild($childReinoudDeclercq->id, $wednesday->id)
                ->selectSupplementForChild($childPietDeclercq->id, $wednesday->id, $supplementIceCream->id)
                ->selectSupplementForChild($childReinoudDeclercq->id, $wednesday->id, $supplementIceCream->id)
                ->waitUntilRequestsSettled()
                ->assertPaidFieldContent('0.00')
                ->screenshot('invoices_registration_week_without_activity')
                ->submitRegistrationFormAndNavigateToNext();

            $dateWeek2 = \Illuminate\Support\Carbon::create(2018, 7, 9);
            $playgroundDayWeek2 = $this->year->playground_days()->get()->filter(function ($playgroundDay) use ($dateWeek2) {
                return $playgroundDay->date()->isSameDay($dateWeek2);
            })->first();
            $browser->navigateToRegistrationsPage($lastDate)
                ->navigateToRegistrationsWithDatePage($dateWeek2)
                ->navigateToRegisterFindFamilyPage($playgroundDayWeek2->week_id)
                ->enterFindFamilyFormData('Reinoud Declercq')
                ->selectFindFamilySuggestion('Veronique')
                ->assertOnEditFamilyRegistrationPage($playgroundDayWeek2->week_id, $familyVeroniqueBaeten->id, 'Veronique Baeten')
                ->selectDayRegistrationForChild($childPietDeclercq->id, $wednesday->id)
                ->selectDayRegistrationForChild($childReinoudDeclercq->id, $wednesday->id)
                ->waitUntilRequestsSettled()
                ->assertPaidFieldContent('0.00')
                ->screenshot('invoices_registration_week_only_separate_days')
                ->submitRegistrationFormAndNavigateToNext();

            $dateWeek3 = \Illuminate\Support\Carbon::create(2018, 7, 16);
            $playgroundDayWeek3 = $this->year->playground_days()->get()->filter(function ($playgroundDay) use ($dateWeek3) {
                return $playgroundDay->date()->isSameDay($dateWeek3);
            })->first();
            $browser->navigateToRegistrationsPage($lastDate)
                ->navigateToRegistrationsWithDatePage($dateWeek3)
                ->navigateToRegisterFindFamilyPage($playgroundDayWeek3->week_id)
                ->enterFindFamilyFormData('Jan Cornelis')
                ->selectFindFamilySuggestion('Erica')
                ->assertOnEditFamilyRegistrationPage($playgroundDayWeek3->week_id, $familyEricaVanHeulen->id, 'Erica Van Heulen')
                ->selectDayRegistrationForChild($childPietDeclercq->id, $wednesday->id)
                ->selectDayRegistrationForChild($childJanCornelis->id, $wednesday->id)
                ->selectDayRegistrationForChild($childJanCornelis->id, $tuesday->id)
                ->selectActivityListRegistrationForChild($childPietDeclercq->id, $activityListNoDate->id)
                ->waitUntilRequestsSettled()
                ->assertPaidFieldContent('0.00')
                ->screenshot('invoices_registration_week_other_family')
                ->submitRegistrationFormAndNavigateToNext();

            $browser->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->openFamilyChildrenDialog($familyVeroniqueBaeten->id)
                ->navigateToChildFamilyInvoice($familyVeroniqueBaeten->id, $childReinoudDeclercq->id)
                ->screenshot('invoice_family_veronique_child_reinoud')
                ->assertSocialContact('SUP')
                ->assertTotal('€ 33.39')
                ->assertLineParticipation(1, '€ 4.00')
                ->assertLineSupplement(1, $supplementIceCream->id, '€ 0.50')
                ->assertLineOther(1, '€ 0.00')
                ->assertLineSubtotal(1, '€ 4.50')
                ->assertLineParticipation(2, '€ 5.00')
                ->assertLineSupplement(2, $supplementIceCream->id, '€ 0.00')
                ->assertLineOther(2, '€ 0.00')
                ->assertLineSubtotal(2, '€ 5.00')
                ->assertLineParticipation(3, '€ 22.50')
                ->assertLineSupplement(3, $supplementIceCream->id, '€ 0.50')
                ->assertLineOther(3, '€ 0.89')
                ->assertLineSubtotal(3, '€ 23.89');

            $browser->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->openFamilyChildrenDialog($familyVeroniqueBaeten->id)
                ->navigateToChildFamilyInvoice($familyVeroniqueBaeten->id, $childPietDeclercq->id)
                ->screenshot('invoice_family_veronique_child_piet')
                ->assertSocialContact('SUP')
                ->assertTotal('€ 31.50')
                ->assertLineParticipation(1, '€ 22.50')
                ->assertLineSupplement(1, $supplementIceCream->id, '€ 0.50')
                ->assertLineOther(1, '€ 0.00')
                ->assertLineSubtotal(1, '€ 23.00')
                ->assertLineParticipation(2, '€ 4.00')
                ->assertLineSupplement(2, $supplementIceCream->id, '€ 0.00')
                ->assertLineOther(2, '€ 0.00')
                ->assertLineSubtotal(2, '€ 4.00')
                ->assertLineParticipation(3, '€ 4.00')
                ->assertLineSupplement(3, $supplementIceCream->id, '€ 0.50')
                ->assertLineOther(3, '€ 0.00')
                ->assertLineSubtotal(3, '€ 4.50');

            $browser->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->openFamilyChildrenDialog($familyEricaVanHeulen->id)
                ->navigateToChildFamilyInvoice($familyEricaVanHeulen->id, $childPietDeclercq->id)
                ->screenshot('invoice_family_erica_child_piet')
                ->assertTotal('€ 7.21')
                ->assertLineParticipation(1, '€ 2.00')
                ->assertLineSupplement(1, $supplementIceCream->id, '€ 0.00')
                ->assertLineOther(1, '€ 0.00')
                ->assertLineSubtotal(1, '€ 2.00')
                ->assertLineOther(2, '€ 5.21')
                ->assertLineSubtotal(2, '€ 5.21');

            $browser->visit(new InternalDashboardPage($this->year->id))
                ->navigateToFamiliesPage()
                ->openFamilyChildrenDialog($familyEricaVanHeulen->id)
                ->navigateToChildFamilyInvoice($familyEricaVanHeulen->id, $childJanCornelis->id)
                ->screenshot('invoice_family_erica_child_jan')
                ->assertTotal('€ 5.00')
                ->assertLineParticipation(1, '€ 2.50')
                ->assertLineSupplement(1, $supplementIceCream->id, '€ 0.00')
                ->assertLineOther(1, '€ 0.00')
                ->assertLineSubtotal(1, '€ 2.50')
                ->assertLineParticipation(2, '€ 2.50')
                ->assertLineSupplement(2, $supplementIceCream->id, '€ 0.00')
                ->assertLineOther(2, '€ 0.00')
                ->assertLineSubtotal(2, '€ 2.50');
        });
    }
}
