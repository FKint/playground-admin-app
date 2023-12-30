<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\DuskTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class InternalDashboardTest extends DuskTestCase
{
    // TODO(fkint): today's registrations
    // TODO(fkint): check overview of registrations per day
    // TODO(fkint): check lists

    /**
     * Check that the dashboard shows the name of the year somewhere.
     */
    public function testShowsYearName()
    {
        $user = \App\Models\User::factory()->create();
        $year = \App\Models\Year::factory()->for($user->organization)->create(['description' => 'The Year of the Pig']);
        $this->browse(function (Browser $browser) use ($user, $year) {
            $browser->loginAs($user)
                ->visit(new InternalDashboardPage($year->id))
                ->assertSee('The Year of the Pig');
        });
    }

    public function testCashRegisterShowsEntries()
    {
        $user = \App\Models\User::factory()->create();
        $year = \App\Models\Year::factory()->create(['organization_id' => $user->organization_id, 'description' => 'The Year of the Pig']);
        $adminSession1 = \App\Models\AdminSession::factory()->create(['year_id' => $year->id, 'responsible_name' => 'The first user']);
        $transaction1 = \App\Models\Transaction::factory()->create(['year_id' => $year->id, 'admin_session_id' => $adminSession1->id, 'created_at' => (new \DateTimeImmutable())->setISODate(2018, 3, 1)]);
        $transaction2 = \App\Models\Transaction::factory()->create(['year_id' => $year->id, 'admin_session_id' => $adminSession1->id]);
        $adminSession2 = \App\Models\AdminSession::factory()->create(['year_id' => $year->id, 'responsible_name' => 'The second user', 'session_end' => null]);
        $transaction3 = \App\Models\Transaction::factory()->create(['year_id' => $year->id, 'admin_session_id' => $adminSession2->id]);

        $this->browse(function (Browser $browser) use ($user, $year) {
            $browser->loginAs($user)
                ->visit(new InternalDashboardPage($year->id))
                ->waitForText('The second user')
                ->waitForText('The first user')
                ->screenshot('internal_dashboard_with_cash_registry');
        });
    }
}
