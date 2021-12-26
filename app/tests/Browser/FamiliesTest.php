<?php

namespace Tests\Browser;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\InitialDataSeeder;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\DuskTestCase;

/**
 * @internal
 * @coversNothing
 */
class FamiliesTest extends DuskTestCase
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
        $this->existingFamily = \App\Family::factory()->create(['year_id' => $this->year->id, 'guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten', 'tariff_id' => $this->normalTariff->id, 'email' => 'family1@test.com']);
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
                    socialContact: '',
                    needsInvoice: null,
                    email: 'family2@test.com'
                )
                ->submitAddFamilySuccessfully(\App\Family::count() + 1);
        });
        $family = \App\Family::find(\App\Family::count());
        $this->assertEquals($family->email, 'family2@test.com');
    }
}
