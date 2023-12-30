<?php

namespace Tests\Browser;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\InitialDataSeeder;
use Illuminate\Support\Carbon;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\Browser\Pages\InternalTransactionHistoryPage;
use Tests\DuskTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TransactionsTest extends DuskTestCase
{
    private $user;
    private $year;
    private $adminSessionSwitchTime;
    private $oldAdminSession;
    private $newAdminSession;
    private $normalTariff;
    private $socialTariff;
    private $family1;
    private $family2;
    private $firstPlaygroundDay;
    private $lastDate;

    public function setUp(): void
    {
        parent::setUp();
        app(DatabaseSeeder::class)->call(InitialDataSeeder::class);
        $this->year = \App\Models\Year::firstOrFail();
        $this->user = \App\Models\User::factory()->create(['organization_id' => $this->year->organization_id]);

        $this->adminSessionSwitchTime = Carbon::create(2020, 12, 1, 12, 0, 0)->toImmutable();
        $this->oldAdminSession = $this->year->getActiveAdminSession();
        $this->oldAdminSession->update(['responsible_name' => 'Closer',
            'counted_cash' => 1000,
            'session_end' => $this->adminSessionSwitchTime,
            'remarks' => '', ]);
        $this->oldAdminSession->save();
        $this->newAdminSession = new \App\Models\AdminSession();
        $this->newAdminSession->year()->associate($this->year);
        $this->newAdminSession->created_at = $this->adminSessionSwitchTime;
        $this->newAdminSession->save(['timestamps' => false]);

        $this->normalTariff = \App\Models\Tariff::whereAbbreviation('NRML')->firstOrFail();
        $this->socialTariff = \App\Models\Tariff::whereAbbreviation('SCL')->firstOrFail();

        $this->firstPlaygroundDay = $this->year->playground_days()->firstOrFail();

        $this->family1 = \App\Models\Family::factory()->create(['year_id' => $this->year->id, 'guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten', 'tariff_id' => $this->normalTariff->id, 'needs_invoice' => false]);
        $this->family2 = \App\Models\Family::factory()->create(['year_id' => $this->year->id, 'guardian_first_name' => 'Ro', 'guardian_last_name' => 'Bot', 'tariff_id' => $this->normalTariff->id, 'needs_invoice' => false]);

        $this->lastDate = Carbon::create(2018, 8, 10);
    }

    public function testDashboardToTransactions()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalDashboardPage($this->year->id))
                ->navigateToTransactionHistory();
        });
    }

    public function testDateNavigation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalTransactionHistoryPage($this->year->id, $this->lastDate))
                ->navigateToPreviousDate($this->lastDate->copy()->subDay());
        });
    }

    public function testTransactionHistoryContent()
    {
        $timestamp1 = $this->adminSessionSwitchTime->subHour();
        $normal_transaction = $this->createTransaction($timestamp1, $this->family1, $this->oldAdminSession, amountPaid: 3.5, amountExpected: 3.5, remarks: 'Normal transaction');
        $timestamp_old = $this->adminSessionSwitchTime->subYear();
        $old_transaction = $this->createTransaction($timestamp_old, $this->family1, $this->oldAdminSession, amountPaid: 10, amountExpected: 10, remarks: 'Old transaction');
        $timestamp2 = $this->adminSessionSwitchTime->addHour();
        $second_transaction = $this->createTransaction($timestamp2, $this->family2, $this->newAdminSession, amountPaid: 3, amountExpected: 10, remarks: 'Not enough cash');

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(new InternalTransactionHistoryPage($this->year->id, $this->adminSessionSwitchTime))
                ->waitForText('Not enough cash')
                ->assertSee('Normal transaction')
                ->assertDontSee('Old transaction');
        });
    }

    private function createTransaction($timestamp, $family, $adminSession, $amountPaid, $amountExpected, $remarks)
    {
        // Unguard Eloquent model to overwrite created_at and updated_at.
        \App\Models\Transaction::unguard();
        $transaction = new \App\Models\Transaction(['amount_paid' => 3.5,
            'amount_expected' => 3.5,
            'remarks' => $remarks, 'created_at' => $timestamp, 'updated_at' => $timestamp, ]);
        $transaction->admin_session()->associate($adminSession);
        $transaction->year()->associate($this->year);
        $transaction->family()->associate($family);
        $transaction->save();
        \App\Models\Transaction::reguard();
        $transaction->refresh();
        $this->assertEquals($timestamp, $transaction->created_at);

        return $transaction;
    }
}
