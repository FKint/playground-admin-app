<?php

namespace Tests\Browser;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\InitialDataSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DuskTestCase;

/**
 * Clicks every PDF / CSV / Excel export button of the DataTables in the admin pages and checks the generated file.
 * The browser may run in a different container than the tests, so instead of reading a download directory, the
 * generated blobs are intercepted in the page.
 *
 * @internal
 *
 * @coversNothing
 */
#[CoversNothing]
class ExportButtonsTest extends DuskTestCase
{
    private $user;
    private $year;
    private $family;
    private $child;
    private $activityList;

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
        $this->family->children()->syncWithoutDetaching([$this->child->id => ['year_id' => $this->year->id]]);
        $this->activityList = \App\Models\ActivityList::factory()->create(['year_id' => $this->year->id, 'name' => 'Kid Rock']);
    }

    public static function pages(): array
    {
        return [
            'children' => ['children', ['pdf', 'csv', 'excel'], 'Declercq'],
            'families' => ['families', ['pdf', 'csv', 'excel'], 'Baeten'],
            'lists' => ['lists', ['pdf', 'csv', 'excel'], null],
            'list participants' => ['list', ['pdf', 'csv', 'excel'], null],
            'registrations' => ['registrations', ['pdf', 'csv', 'excel'], null],
            'transactions' => ['transactions', ['pdf', 'csv', 'excel'], null],
            'family transactions' => ['family_transactions', ['pdf', 'csv', 'excel'], null],
        ];
    }

    #[DataProvider('pages')]
    public function testExportButtons(string $page, array $formats, ?string $expectedContent)
    {
        $url = $this->url($page);
        $this->browse(function (Browser $browser) use ($url, $formats, $expectedContent) {
            $browser->loginAs($this->user)->visit($url);
            $browser->waitFor('.dt-buttons', 10);
            foreach ($formats as $format) {
                $browser->waitFor('.buttons-'.$format);
                $browser->script($this->interceptScript());
                $browser->click('.buttons-'.$format);
                $browser->waitUsing(10, 250, fn () => $browser->script('return window.__exports.length + window.__errors.length')[0] > 0);
                $result = $browser->script('return {exports: window.__exports, errors: window.__errors}')[0];
                $this->assertEmpty($result['errors'], "$format export raised: ".json_encode($result['errors']));
                $this->assertCount(1, $result['exports'], "$format export produced no file");
                $this->assertExport($format, $result['exports'][0], $expectedContent);
            }
        });
    }

    private function assertExport(string $format, array $export, ?string $expectedContent): void
    {
        $this->assertGreaterThan(0, $export['size'], "$format file is empty");
        switch ($format) {
            case 'pdf':
                $this->assertStringStartsWith('%PDF-', $export['head']);
                break;
            case 'excel':
                $this->assertStringStartsWith('PK', $export['head']);
                break;
            case 'csv':
                $this->assertStringContainsString('csv', $export['type']);
                if ($expectedContent) {
                    $this->assertStringContainsString($expectedContent, $export['text']);
                }
                break;
        }
    }

    private function url(string $page): string
    {
        $year = ['year' => $this->year->id];
        switch ($page) {
            case 'list':
                return route('internal.show_list', $year + ['list' => $this->activityList->id]);
            case 'registrations':
                return route('internal.registrations_for_date', $year + ['date' => $this->year->playground_days()->firstOrFail()->date()->format('Y-m-d')]);
            case 'transactions':
                return route('internal.show_transactions_for_date', $year);
            case 'family_transactions':
                return route('internal.show_family_transactions', $year + ['family' => $this->family->id]);
            default:
                return route('internal.'.$page, $year);
        }
    }

    /** Records every blob that the page offers for download, and any error raised while generating it. */
    private function interceptScript(): string
    {
        return <<<'JS'
            window.__exports = [];
            window.__errors = [];
            if (!window.__interceptInstalled) {
                window.__interceptInstalled = true;
                const original = URL.createObjectURL.bind(URL);
                URL.createObjectURL = function (blob) {
                    blob.arrayBuffer().then(function (buffer) {
                        const head = String.fromCharCode.apply(null, new Uint8Array(buffer.slice(0, 8)));
                        blob.text().then(function (text) {
                            window.__exports.push({size: blob.size, type: blob.type, head: head, text: text.slice(0, 5000)});
                        });
                    });
                    return original(blob);
                };
                window.addEventListener('error', function (e) { window.__errors.push(String(e.message)); });
                window.addEventListener('unhandledrejection', function (e) { window.__errors.push(String(e.reason)); });
            }
JS;
    }
}
