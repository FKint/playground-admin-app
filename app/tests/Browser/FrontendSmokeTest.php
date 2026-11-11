<?php

namespace Tests\Browser;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\InitialDataSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DuskTestCase;

/**
 * Guards the frontend libraries (jQuery, Bootstrap, DataTables, datepicker, autocomplete) against regressions when
 * they are upgraded: every page must load without JavaScript errors, and each widget must still work in a real browser.
 *
 * @internal
 *
 * @coversNothing
 */
#[CoversNothing]
class FrontendSmokeTest extends DuskTestCase
{
    private $user;
    private $year;
    private $family;
    private $child;
    private $activityList;
    private $week;

    public function setUp(): void
    {
        parent::setUp();
        app(DatabaseSeeder::class)->call(InitialDataSeeder::class);
        $this->year = \App\Models\Year::firstOrFail();
        $this->user = \App\Models\User::factory()->create(['organization_id' => $this->year->organization_id]);

        $tariff = \App\Models\Tariff::whereAbbreviation('NRML')->firstOrFail();
        $ageGroup = \App\Models\AgeGroup::whereAbbreviation('6-12')->firstOrFail();
        $this->family = \App\Models\Family::factory()->create(['year_id' => $this->year->id, 'guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten', 'tariff_id' => $tariff->id, 'needs_invoice' => false]);
        $this->child = \App\Models\Child::factory()->create(['year_id' => $this->year->id, 'first_name' => 'Reinoud', 'last_name' => 'Declercq', 'age_group_id' => $ageGroup->id]);
        \App\Models\Child::factory()->create(['year_id' => $this->year->id, 'first_name' => 'Zacharias', 'last_name' => 'Andries', 'age_group_id' => $ageGroup->id]);
        $this->family->children()->syncWithoutDetaching([$this->child->id => ['year_id' => $this->year->id]]);
        $this->activityList = \App\Models\ActivityList::factory()->create(['year_id' => $this->year->id, 'name' => 'Kid Rock']);
        $this->week = $this->year->playground_days()->firstOrFail()->week_id;
    }

    private function url(string $name, array $extra = []): string
    {
        return route($name, ['year' => $this->year->id] + $extra);
    }

    public static function pages(): array
    {
        return [
            'dashboard' => ['internal.dashboard'],
            'children' => ['internal.children'],
            'families' => ['internal.families'],
            'lists' => ['internal.lists'],
            'list details' => ['internal.show_list', ['list' => 'activityList']],
            'registrations' => ['internal.registrations'],
            'find family' => ['internal.show_find_family_registration', ['week' => 'week']],
            'transactions' => ['internal.show_transactions_for_date'],
            'settings' => ['internal.settings'],
        ];
    }

    #[DataProvider('pages')]
    public function testPagesLoadWithoutJavascriptErrors(string $route, array $params = [])
    {
        $params = array_map(fn ($p) => is_string($p) && isset($this->$p) ? (is_object($this->$p) ? $this->$p->id : $this->$p) : $p, $params);
        $url = $this->url($route, $params);
        $this->browse(function (Browser $browser) use ($url) {
            $browser->loginAs($this->user)->visit($url)->waitFor('#top-navbar');
            $globals = $browser->script(<<<'JS'
                return {
                    jquery: window.jQuery && window.jQuery.fn.jquery,
                    dataTable: typeof window.jQuery.fn.DataTable,
                    modal: typeof (window.bootstrap && window.bootstrap.Modal),
                    datepicker: typeof window.Datepicker,
                    autocomplete: typeof window.autocomplete,
                };
JS)[0];
            $this->assertStringStartsWith('4.', $globals['jquery']);
            $this->assertSame('function', $globals['dataTable']);
            $this->assertSame('function', $globals['modal']);
            $this->assertSame('function', $globals['datepicker']);
            $this->assertSame('function', $globals['autocomplete']);

            $errors = collect($browser->driver->manage()->getLog('browser'))
                ->filter(fn ($entry) => $entry['level'] === 'SEVERE')
                ->pluck('message')->all();
            $this->assertEmpty($errors, "JavaScript errors on $url:\n".implode("\n", $errors));
        });
    }

    public function testDataTableRendersSearchesAndSorts()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)->visit($this->url('internal.children'))
                ->waitFor('#children-table tbody tr')
                ->assertSee('Declercq')->assertSee('Andries')
                ->assertPresent('.dt-paging')
                ->type('.dt-search input', 'Declercq')
                ->waitUntilMissingText('Andries')
                ->assertSee('Declercq')
                ->keys('.dt-search input', ...array_fill(0, 8, '{backspace}'))
                ->waitForText('Andries');
            // Sorting: clicking the "last name" header toggles the sort order.
            $browser->click('#children-table thead th:nth-child(2)');
            $this->assertContains($browser->attribute('#children-table thead th:nth-child(2)', 'aria-sort'), ['ascending', 'descending']);
        });
    }

    public function testModalOpensTabsSwitchAndModalCloses()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)->visit($this->url('internal.children'))
                ->waitFor('#children-table .btn-edit-child')
                ->click('#children-table .btn-edit-child')
                ->waitFor('#edit-child-modal.show')
                ->assertVisible('#edit-child-info-div')
                ->click('#edit-child-tablist [data-bs-toggle="tab"][href="#edit-child-families-div"]')
                ->waitFor('#edit-child-families-div.show.active')
                ->assertVisible('#edit-child-families-div')
                ->assertMissing('#edit-child-info-div.active')
                ->click('#btn-close-edit-child')
                ->waitUntilMissing('#edit-child-modal.show');
        });
    }

    public function testNavbarDropdownOpens()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)->visit($this->url('internal.dashboard'))
                ->assertMissing('.dropdown-menu.show')
                ->clickLink('Extra')
                ->waitFor('.dropdown-menu.show')
                ->assertSeeLink('Uitloggen');
        });
    }

    public function testDatepickerOpensAndNavigatesToTheChosenDate()
    {
        $date = $this->year->playground_days()->firstOrFail()->date();
        $this->browse(function (Browser $browser) use ($date) {
            $browser->loginAs($this->user)->visit($this->url('internal.registrations_for_date', ['date' => $date->format('Y-m-d')]))
                ->assertInputValue('#registration-datepicker input', $date->format('Y-m-d'))
                ->click('#registration-datepicker input')
                ->waitFor('.datepicker-picker')
                ->assertVisible('.datepicker-picker .datepicker-cell.selected')
                // Pick the following day in the calendar.
                ->click('.datepicker-picker .datepicker-cell.selected + .datepicker-cell')
                ->waitUntilMissing('.datepicker-picker')
                ->waitForLocation(parse_url($this->url('internal.registrations_for_date', ['date' => $date->copy()->addDay()->format('Y-m-d')]), PHP_URL_PATH));
            // The previous/next day buttons navigate as well.
            $browser->click('#btn-prev-day')
                ->waitForLocation(parse_url($this->url('internal.registrations_for_date', ['date' => $date->format('Y-m-d')]), PHP_URL_PATH));
        });
    }

    public function testAutocompleteSuggestsAndSelects()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)->visit($this->url('internal.show_find_family_registration', ['week' => $this->week]))
                ->waitFor('#family-search')
                ->type('#family-search', 'Baeten')
                ->waitFor('.autocomplete-menu a.list-group-item')
                ->assertSeeIn('.autocomplete-menu', 'Baeten')
                ->click('.autocomplete-menu a.list-group-item')
                ->waitForLocation(parse_url($this->url('internal.show_edit_registration', ['week' => $this->week, 'family' => $this->family->id]), PHP_URL_PATH));
        });
    }

    public function testAutocompleteShowsMessageWithoutResults()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)->visit($this->url('internal.show_find_family_registration', ['week' => $this->week]))
                ->type('#family-search', 'Zzzzzz')
                ->waitForText('Nothing found.')
                ->keys('#family-search', '{escape}')
                ->waitUntilMissingText('Nothing found.');
        });
    }
}
