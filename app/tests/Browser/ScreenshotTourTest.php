<?php

namespace Tests\Browser;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\InitialDataSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\DuskTestCase;

/**
 * Takes screenshots of the main pages and widgets (modal, datepicker, autocomplete, DataTables) from deterministic
 * data, so two versions of the frontend can be compared side by side. The files are written to
 * tests/Browser/screenshots/tour (or $SCREENSHOT_DIR) and are not asserted on.
 *
 * Volatile content (version footer, "today" highlight) is masked so equal markup gives equal pixels.
 *
 * @internal
 *
 * @coversNothing
 */
#[CoversNothing]
class ScreenshotTourTest extends DuskTestCase
{
    private $user;
    private $year;
    private $family;
    private $week;

    public function setUp(): void
    {
        parent::setUp();
        app(DatabaseSeeder::class)->call(InitialDataSeeder::class);
        $this->year = \App\Models\Year::firstOrFail();
        $this->user = \App\Models\User::factory()->create(['organization_id' => $this->year->organization_id]);

        $tariff = \App\Models\Tariff::whereAbbreviation('NRML')->firstOrFail();
        $ageGroup = \App\Models\AgeGroup::whereAbbreviation('6-12')->firstOrFail();
        $this->family = \App\Models\Family::factory()->create([
            'year_id' => $this->year->id, 'guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten',
            'tariff_id' => $tariff->id, 'needs_invoice' => false, 'email' => 'veronique@example.com',
            'contact' => '0470 00 00 00', 'social_contact' => null, 'remarks' => null,
        ]);
        foreach ([['Reinoud', 'Declercq', 2014], ['Zacharias', 'Andries', 2012]] as [$first, $last, $year]) {
            $child = \App\Models\Child::factory()->create([
                'year_id' => $this->year->id, 'first_name' => $first, 'last_name' => $last,
                'birth_year' => $year, 'age_group_id' => $ageGroup->id, 'remarks' => null,
            ]);
            $this->family->children()->syncWithoutDetaching([$child->id => ['year_id' => $this->year->id]]);
        }
        \App\Models\ActivityList::factory()->create([
            'year_id' => $this->year->id, 'name' => 'Kid Rock', 'price' => 5, 'date' => $this->year->playground_days()->firstOrFail()->date(),
        ]);
        $this->week = $this->year->playground_days()->firstOrFail()->week_id;
    }

    private function shot(Browser $browser, string $name): void
    {
        $browser->pause(400)->script(<<<'JS'
            document.querySelectorAll('.container').forEach(e => { if (e.textContent.includes('Version info')) e.style.visibility = 'hidden'; });
            document.querySelectorAll('.today, .focused').forEach(e => e.classList.remove('today', 'focused'));
JS);
        $browser->screenshot('tour/'.$name);
    }

    public function testTour()
    {
        $date = $this->year->playground_days()->firstOrFail()->date()->format('Y-m-d');
        $year = ['year' => $this->year->id];
        $this->browse(function (Browser $browser) use ($date, $year) {
            $browser->resize(1280, 900);
            $browser->visit(route('login'))->waitFor('form');
            $this->shot($browser, '01-login');

            $browser->loginAs($this->user)->visit(route('internal.dashboard', $year))->waitFor('#top-navbar');
            $this->shot($browser, '02-dashboard');

            $browser->visit(route('internal.children', $year))->waitFor('#children-table tbody tr');
            $this->shot($browser, '03-children-table');
            $browser->type('.dataTables_filter input, .dt-search input', 'Declercq')->pause(500);
            $this->shot($browser, '04-children-table-filtered');

            $browser->visit(route('internal.children', $year))->waitFor('#children-table .btn-edit-child')
                ->click('#children-table .btn-edit-child')->waitFor('[dusk="edit-child-form"]')->pause(800);
            $this->shot($browser, '05-edit-child-modal');
            $browser->click('#edit-child-tablist li[data-tag="families"] > a')->pause(800);
            $this->shot($browser, '06-edit-child-modal-families-tab');

            $browser->visit(route('internal.families', $year))->waitFor('table tbody tr');
            $this->shot($browser, '07-families');
            $browser->visit(route('internal.lists', $year))->waitFor('table');
            $this->shot($browser, '08-lists');

            $browser->visit(route('internal.registrations_for_date', $year + ['date' => $date]))->waitFor('#registration-datepicker');
            $this->shot($browser, '09-registrations');
            $browser->click('#registration-datepicker input')->pause(500);
            $this->shot($browser, '10-registrations-datepicker-open');

            $browser->visit(route('internal.show_find_family_registration', $year + ['week' => $this->week]))->waitFor('#family-search')
                ->type('#family-search', 'Baeten')->waitFor('.tt-menu .list-group-item, .autocomplete-menu .list-group-item')->pause(500);
            $this->shot($browser, '11-find-family-autocomplete');

            $browser->visit(route('internal.show_edit_registration', $year + ['week' => $this->week, 'family' => $this->family->id]))->pause(1500);
            $this->shot($browser, '12-edit-registration');

            $browser->visit(route('internal.show_transactions_for_date', $year + ['date' => $date]))->waitFor('#transactions-table');
            $this->shot($browser, '13-transactions');
            $browser->visit(route('internal.settings', $year))->waitFor('#top-navbar');
            $this->shot($browser, '14-settings');
            $browser->visit(route('internal.dashboard', $year))->clickLink('Extra')->pause(500);
            $this->shot($browser, '15-navbar-dropdown');
            $browser->resize(500, 900)->visit(route('internal.dashboard', $year))->pause(500);
            $this->shot($browser, '16-dashboard-mobile');
        });
    }
}
