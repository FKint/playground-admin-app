<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InternalDashboardPage;
use Tests\Browser\Pages\UserDashboardPage;
use Tests\DuskTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UserDashboardTest extends DuskTestCase
{
    private $user1;
    private $year1;
    private $year2;
    private $year3;

    public function setUp(): void
    {
        parent::setUp();
        $this->user1 = \App\Models\User::factory()->create();
        $this->year1 = \App\Models\Year::factory()->create(['organization_id' => $this->user1->organization_id, 'description' => 'Year 1']);
        $this->year2 = \App\Models\Year::factory()->create(['organization_id' => $this->user1->organization_id, 'description' => 'Year 2']);

        $user2 = \App\Models\User::factory()->create();
        $this->year3 = \App\Models\Year::factory()->create(['organization_id' => $user2->organization_id, 'description' => 'Year 3 for user 2']);
    }

    /**
     * User dashboard shows links to relevant years.
     */
    public function testUserDashboardShowsYearLinksAndCanNavigate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user1)
                ->visit(new UserDashboardPage())
                ->assertShowsYear($this->year1->description)
                ->assertShowsYear($this->year2->description)
                ->assertDontShowYear($this->year3->description)
                ->navigateToYearDashboard($this->year2->description)
                ->on(new InternalDashboardPage($this->year2->id));
        });
    }
}
