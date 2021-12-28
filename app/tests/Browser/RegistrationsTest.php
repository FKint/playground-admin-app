<?php

namespace Tests\Browser;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\InitialDataSeeder;
use Illuminate\Support\Carbon;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalEditFamilyRegistrationPage;
use Tests\DuskTestCase;

/**
 * @internal
 * @coversNothing
 */
class RegistrationsTest extends DuskTestCase
{
    private $user;
    private $year;
    private $normalTariff;
    private $socialTariff;
    private $ageGroup612;
    private $ageGroupKls;
    private $child;
    private $family;
    private $firstPlaygroundDay;
    private $lastDate;

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

        $this->firstPlaygroundDay = $this->year->playground_days()->firstOrFail();
        $this->family = \App\Family::factory()->create(['year_id' => $this->year->id, 'guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten', 'tariff_id' => $this->normalTariff->id, 'needs_invoice' => false]);
        $this->child = \App\Child::factory()->create(['year_id' => $this->year->id, 'first_name' => 'Reinoud', 'last_name' => 'Declercq', 'age_group_id' => $this->ageGroupKls->id]);
        $this->family->children()->syncWithoutDetaching([$this->child->id => ['year_id' => $this->year->id]]);
        $this->lastDate = Carbon::create(2018, 8, 10);
    }

    public function testCashRegistration()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalEditFamilyRegistrationPage($this->year->id, $this->firstPlaygroundDay->week_id, $this->family->id))
                ->selectWeekRegistrationforChild($this->child->id)
                ->waitUntilRequestsSettled()
                ->assertExpectedAmount('22.50')
                ->assertPaidFieldContent('0.00')
                ->enterPaidField('22.50')
                ->submitRegistrationFormAndNavigate($this->lastDate);
        });

        $this->family->refresh();
        $transaction = $this->family->transactions()->firstOrFail();
        $this->assertEquals('22.50', $transaction->amount_paid);
    }

    public function testCashRegistrationWrongPaidAmount()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalEditFamilyRegistrationPage($this->year->id, $this->firstPlaygroundDay->week_id, $this->family->id))
                ->selectWeekRegistrationforChild($this->child->id)
                ->waitUntilRequestsSettled()
                ->assertExpectedAmount('22.50')
                ->assertPaidFieldContent('0.00')
                ->enterPaidField('10.50')
                ->submitRegistrationFormAndExpectError()
                ->enterRemarksField('Not enough cash. Will pay tomorrow')
                ->submitRegistrationFormAndNavigate($this->lastDate);
        });

        $this->family->refresh();
        $transaction = $this->family->transactions()->firstOrFail();
        $this->assertEquals('10.50', $transaction->amount_paid);
    }
}
