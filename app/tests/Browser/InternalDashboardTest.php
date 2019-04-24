<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\DuskTestCase;

class InternalDashboardTest extends DuskTestCase
{
    // TODO(fkint): today's registrations
    // TODO(fkint): check overview of registrations per day
    // TODO(fkint): check lists

    /**
     * Check that the dashboard shows the name of the year somewhere.
     *
     * @return void
     */
    public function testShowsYearName()
    {
        $user = factory(\App\User::class)->create();
        $year = factory(\App\Year::class)->create(['organization_id' => $user->organization_id, 'description' => 'The Year of the Pig']);
        $this->browse(function (Browser $browser) use ($user, $year) {
            $browser->loginAs($user)
                ->visit(new InternalDashboardPage($year->id))
                ->assertSee('The Year of the Pig');
        });
    }

    public function testCashRegisterShowsEntries()
    {
        $user = factory(\App\User::class)->create();
        $year = factory(\App\Year::class)->create(['organization_id' => $user->organization_id, 'description' => 'The Year of the Pig']);
        $adminSession1 = factory(\App\AdminSession::class)->create(['year_id' => $year->id, 'responsible_name' => 'The first user']);
        $transaction1 = factory(\App\Transaction::class)->create(['year_id' => $year->id, 'admin_session_id' => $adminSession1->id, 'created_at' => (new \DateTimeImmutable())->setISODate(2018, 3, 1)]);
        $transaction2 = factory(\App\Transaction::class)->create(['year_id' => $year->id, 'admin_session_id' => $adminSession1->id]);
        $adminSession2 = factory(\App\AdminSession::class)->create(['year_id' => $year->id, 'responsible_name' => 'The second user', 'session_end' => null]);
        $transaction3 = factory(\App\Transaction::class)->create(['year_id' => $year->id, 'admin_session_id' => $adminSession2->id]);

        $this->browse(function (Browser $browser) use ($user, $year) {
            $browser->loginAs($user)
                ->visit(new InternalDashboardPage($year->id))
                ->assertSee("The second user")
                ->assertSee("The first user")
                ->screenshot('internal_dashboard_with_cash_registry');
        });
    }
}
