<?php

namespace Tests\Browser;

use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\InitialDataSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\DuskTestCase;

/**
 * Pixel comparison of every page, modal, datepicker and autocomplete against committed baseline screenshots
 * (tests/Browser/baselines), so frontend library upgrades cannot silently change the look.
 *
 * - Data is deterministic (fixed names, dates, timestamps); volatile content (version footer, "today" highlight) is masked.
 * - The comparison runs inside the browser (canvas), so it needs neither GD nor Imagick, also with a remote selenium.
 * - A scene fails when more than MAX_DIFFERENT_RATIO of the pixels differ by more than CHANNEL_TOLERANCE.
 *   The actual screenshots and a diff image (differences in red) are written to tests/Browser/screenshots/visual.
 * - Accept intended changes with: UPDATE_VISUAL_BASELINES=1 php artisan dusk --filter=VisualRegressionTest
 *   Baselines should be generated with the same browser as CI (the selenium image), see readme.md.
 *
 * @internal
 *
 * @coversNothing
 */
#[CoversNothing]
class VisualRegressionTest extends DuskTestCase
{
    private const CHANNEL_TOLERANCE = 6;
    private const MAX_DIFFERENT_RATIO = 0.005;

    private $user;
    private $year;
    private $family;
    private $child;
    private $list;
    private $week;
    private $date;
    private $adminSession;
    private array $failures = [];

    public function setUp(): void
    {
        parent::setUp();
        app(DatabaseSeeder::class)->call(InitialDataSeeder::class);
        $this->year = \App\Models\Year::firstOrFail();
        $this->user = \App\Models\User::factory()->create(['organization_id' => $this->year->organization_id, 'name' => 'Visual Tester', 'email' => 'visual@example.com']);

        // TODO: fix at the root: InitialDataSeeder stamps the age group dates with the current time of day (they even show up in
        // the settings table as '2003-01-01 14:53:18'). Store plain dates there and drop this workaround.
        // Until then, pin the time so the pages are deterministic.
        foreach (\App\Models\AgeGroup::all() as $group) {
            $group->update(['start_date' => Carbon::parse($group->start_date)->startOfDay(), 'end_date' => Carbon::parse($group->end_date)->startOfDay()]);
        }
        $tariff = \App\Models\Tariff::whereAbbreviation('NRML')->firstOrFail();
        $ageGroup = \App\Models\AgeGroup::whereAbbreviation('6-12')->firstOrFail();
        $this->family = \App\Models\Family::factory()->create([
            'year_id' => $this->year->id, 'guardian_first_name' => 'Veronique', 'guardian_last_name' => 'Baeten',
            'tariff_id' => $tariff->id, 'needs_invoice' => false, 'email' => 'veronique@example.com',
            'contact' => '0470 00 00 00', 'social_contact' => null, 'remarks' => null,
        ]);
        $second = \App\Models\Family::factory()->create([
            'year_id' => $this->year->id, 'guardian_first_name' => 'Ro', 'guardian_last_name' => 'Bot',
            'tariff_id' => $tariff->id, 'needs_invoice' => true, 'email' => null, 'contact' => null, 'social_contact' => null, 'remarks' => 'Factuur',
        ]);
        foreach ([['Reinoud', 'Declercq', 2014], ['Zacharias', 'Andries', 2012]] as [$first, $last, $birthYear]) {
            $child = \App\Models\Child::factory()->create([
                'year_id' => $this->year->id, 'first_name' => $first, 'last_name' => $last,
                'birth_year' => $birthYear, 'age_group_id' => $ageGroup->id, 'remarks' => null,
            ]);
            $this->family->children()->syncWithoutDetaching([$child->id => ['year_id' => $this->year->id]]);
            $this->child ??= $child;
        }
        \App\Models\Child::factory()->create([
            'year_id' => $this->year->id, 'first_name' => 'Yannick', 'last_name' => 'Vermeulen',
            'birth_year' => 2013, 'age_group_id' => $ageGroup->id, 'remarks' => null,
        ]);
        $this->date = $this->year->playground_days()->firstOrFail()->date();
        $this->week = $this->year->playground_days()->firstOrFail()->week_id;
        $this->list = \App\Models\ActivityList::factory()->create([
            'year_id' => $this->year->id, 'name' => 'Kid Rock', 'price' => 5, 'date' => $this->date,
            // The factory randomizes these flags; the list only shows on the attendance form/dashboard when they are true.
            'show_on_attendance_form' => true, 'show_on_dashboard' => true,
        ]);

        $switch = Carbon::create(2020, 12, 1, 12, 0, 0)->toImmutable();
        $this->adminSession = $this->year->getActiveAdminSession();
        $this->adminSession->update(['responsible_name' => 'Closer', 'counted_cash' => 1000, 'session_end' => $switch, 'remarks' => '']);
        $activeAdminSession = new \App\Models\AdminSession();
        $activeAdminSession->year()->associate($this->year);
        $activeAdminSession->created_at = $switch;
        $activeAdminSession->save(['timestamps' => false]);
        foreach ([[$this->family, $switch->subHour(), 3.5, 3.5, 'Normal transaction'], [$second, $switch->subMinutes(30), 3, 10, 'Not enough cash']] as [$family, $at, $paid, $expected, $remarks]) {
            $transaction = \App\Models\Transaction::factory()->create([
                'year_id' => $this->year->id, 'family_id' => $family->id, 'admin_session_id' => $this->adminSession->id,
                'amount_paid' => $paid, 'amount_expected' => $expected, 'remarks' => $remarks,
            ]);
            $transaction->forceFill(['created_at' => $at, 'updated_at' => $at])->saveQuietly();
        }
    }

    private function url(string $name, array $extra = []): string
    {
        return route($name, ['year' => $this->year->id] + $extra);
    }

    public function testPagesMatchBaselines()
    {
        $this->browse(function (Browser $b) {
            $b->resize(1280, 900);

            // Public pages
            $this->scene($b, 'login', fn () => $b->visit(route('login'))->waitFor('form'));
            $this->scene($b, 'register', fn () => $b->visit(route('register'))->waitFor('form'));
            $this->scene($b, 'password-reset-request', fn () => $b->visit(route('password.request'))->waitFor('form'));
            $this->scene($b, 'login-with-error', fn () => $b->visit(route('login'))->type('email', 'nobody@example.com')->type('password', 'wrong')->press('Login')->waitFor('.invalid-feedback, .help-block, .alert'));

            $b->loginAs($this->user);
            $this->scene($b, 'user-dashboard', fn () => $b->visit(route('internal.user_dashboard'))->waitFor('#top-navbar'));

            // Dashboard
            $this->scene($b, 'dashboard', fn () => $b->visit($this->url('internal.dashboard'))->waitFor('#top-navbar'));
            $this->scene($b, 'navbar-dropdown', fn () => $b->visit($this->url('internal.dashboard'))->clickLink('Extra')->waitFor('.open .dropdown-menu'));
            $this->scene($b, 'navbar-mobile-collapsed', fn () => $b->resize(500, 900)->visit($this->url('internal.dashboard'))->waitFor('.navbar-toggle'));
            $this->scene($b, 'navbar-mobile-expanded', fn () => $b->click('.navbar-toggle')->waitFor('#navbar.in')->pause(500));
            $b->resize(1280, 900);

            // Children: DataTable, filtering, modals (new / edit with tabs / info with tabs)
            $children = $this->url('internal.children');
            $this->scene($b, 'children-table', fn () => $b->visit($children)->waitFor('#children-table tbody tr'));
            $this->scene($b, 'children-table-filtered', fn () => $b->type('.dt-search input', 'Declercq')->waitUntilMissingText('Andries'));
            $this->scene($b, 'children-table-sorted', fn () => $b->visit($children)->waitFor('#children-table tbody tr')->click('#children-table thead th:nth-child(2)')->pause(300));
            $this->scene($b, 'children-new-modal', fn () => $b->visit($children)->waitFor('#btn-new-child')->click('#btn-new-child')->waitFor('#new-child-modal.in [dusk=first_name]')->pause(800));
            $this->scene($b, 'children-new-modal-validation', fn () => $b->click('#new-child-modal [dusk=submit]')->waitFor('#new-child-errors-div:not(.hidden)')->pause(300));
            $this->scene($b, 'children-edit-modal', fn () => $b->visit($children)->waitFor('#children-table .btn-edit-child')->click('#children-table .btn-edit-child')->waitFor('[dusk="edit-child-form"]')->pause(800));
            $this->scene($b, 'children-edit-modal-families-tab', fn () => $b->click('#edit-child-tablist li[data-tag="families"] > a')->waitFor('#edit-child-families-div.active')->waitFor('#family-search-ts-control')->pause(800));
            $this->scene($b, 'children-edit-modal-autocomplete', fn () => $b->type('#family-search-ts-control', 'Bot')->waitFor('.ts-dropdown .option')->pause(300));
            $this->scene($b, 'children-info-modal', fn () => $b->visit($children)->waitFor('#children-table .btn-show-child-info')->click('#children-table .btn-show-child-info')->waitFor('#info-child-modal.in form')->pause(800));
            $this->scene($b, 'children-info-modal-families-tab', fn () => $b->click('#info-child-tablist a[href="#info-child-families-div"]')->waitFor('#info-child-families-div.active')->pause(500));

            // Families: DataTable, modals
            $families = $this->url('internal.families');
            $this->scene($b, 'families-table', fn () => $b->visit($families)->waitFor('#families-table tbody tr'));
            $this->scene($b, 'families-edit-modal', fn () => $b->visit($families)->waitFor('.btn-edit-family')->click('.btn-edit-family')->waitFor('[dusk="edit-family-form"]')->pause(800));
            $this->scene($b, 'families-children-modal', fn () => $b->visit($families)->waitFor('.btn-show-family-children')->click('.btn-show-family-children')->waitFor('#family-children-modal.in table')->pause(800));
            $this->scene($b, 'family-new', fn () => $b->visit($this->url('internal.show_new_family_with_children'))->waitFor('[dusk="new-family-form"]'));
            $this->scene($b, 'family-add-child', fn () => $b->visit($this->url('internal.show_add_child_to_family', ['family' => $this->family->id]))->waitFor('#child-search-ts-control'));
            $this->scene($b, 'family-add-child-autocomplete', function () use ($b) {
                $b->script('document.getElementById("child-search").scrollIntoView({block: "center"})');
                $b->type('#child-search-ts-control', 'Verm')->waitFor('.ts-dropdown .option')->pause(300);
            }, keepScroll: true);
            $this->scene($b, 'family-transactions', fn () => $b->visit($this->url('internal.show_family_transactions', ['family' => $this->family->id]))->waitFor('table')->pause(500));

            // Lists: DataTable, forms with datepicker, participants autocomplete
            $this->scene($b, 'lists-table', fn () => $b->visit($this->url('internal.lists'))->waitFor('table'));
            $this->scene($b, 'list-new', fn () => $b->visit($this->url('internal.show_create_new_list'))->waitFor('#txt-list-date'));
            $this->scene($b, 'list-new-datepicker', fn () => $b->click('#txt-list-date')->waitFor('.datepicker-dropdown')->pause(500));
            $this->scene($b, 'list-details', fn () => $b->visit($this->url('internal.show_list', ['list' => $this->list->id]))->waitFor('#child-family-search-ts-control'));
            $this->scene($b, 'list-details-autocomplete', fn () => $b->type('#child-family-search-ts-control', 'Declercq')->waitFor('.ts-dropdown .option')->pause(300));
            $this->scene($b, 'list-details-datepicker', fn () => $b->click('#txt-list-date')->waitFor('.datepicker-dropdown')->pause(500));

            // Registrations
            $registrations = $this->url('internal.registrations_for_date', ['date' => $this->date->format('Y-m-d')]);
            $this->scene($b, 'registrations', fn () => $b->visit($registrations)->waitFor('#registration-datepicker'));
            $this->scene($b, 'registrations-datepicker', fn () => $b->click('#registration-datepicker input')->waitFor('.datepicker-dropdown')->pause(500));
            $this->scene($b, 'registrations-no-playground-day', fn () => $b->visit($this->url('internal.registrations_for_date', ['date' => '2018-01-01']))->waitFor('.alert'));
            $this->scene($b, 'registrations-find-family', fn () => $b->visit($this->url('internal.show_find_family_registration', ['week' => $this->week]))->waitFor('#family-search-ts-control'));
            $this->scene($b, 'registrations-find-family-autocomplete', fn () => $b->type('#family-search-ts-control', 'Baeten')->waitFor('.ts-dropdown .option')->pause(300));
            $this->scene($b, 'registrations-find-family-no-results', fn () => $b->visit($this->url('internal.show_find_family_registration', ['week' => $this->week]))->type('#family-search-ts-control', 'Zzzzz')->waitForText('Nothing found.')->pause(300));
            $edit = $this->url('internal.show_edit_registration', ['week' => $this->week, 'family' => $this->family->id]);
            $this->scene($b, 'registration-edit', fn () => $b->visit($edit)->waitUntilMissing('#loading-indicator:not([style*="display: none"])')->waitForText('Kid Rock')->pause(1000));
            $this->scene($b, 'registration-edit-checked', fn () => $b->check('.day-registration .registration-checkbox')->check('.whole-week-registration .registration-checkbox')->waitForText('Kid Rock')->pause(800));

            // Transactions, settings, admin sessions
            $transactions = $this->url('internal.show_transactions_for_date', ['date' => '2020-12-01']);
            $this->scene($b, 'transactions', fn () => $b->visit($transactions)->waitFor('#transactions-table tbody tr'));
            $this->scene($b, 'transactions-datepicker', fn () => $b->click('#transactions-datepicker input')->waitFor('.datepicker-dropdown')->pause(500));
            $this->scene($b, 'settings', fn () => $b->visit($this->url('internal.settings'))->waitFor('#top-navbar')->pause(500));
            $this->scene($b, 'admin-session-close', fn () => $b->visit($this->url('internal.close_admin_session'))->waitFor('form'));
            $this->scene($b, 'admin-session-edit', fn () => $b->visit($this->url('internal.show_edit_admin_session', ['admin_session' => $this->adminSession->id]))->waitFor('form'));
        });

        $this->assertNoVisualDifferences();
    }

    /**
     * The exported PDFs (client-side DataTables/pdfmake exports and the server-side dompdf invoice), first page, rendered
     * by the browser's PDF viewer. Guards the pdfmake bundle/chunking and the export column/row selection.
     */
    public function testPdfsMatchBaselines()
    {
        $this->browse(function (Browser $b) {
            $b->resize(1280, 900)->loginAs($this->user);
            $day = $this->date->format('Y-m-d');
            $pages = [
                'pdf-children' => $this->url('internal.children'),
                'pdf-families' => $this->url('internal.families'),
                'pdf-lists' => $this->url('internal.lists'),
                'pdf-list-participants' => $this->url('internal.show_list', ['list' => $this->list->id]),
                'pdf-registrations' => $this->url('internal.registrations_for_date', ['date' => $day]),
                'pdf-transactions' => $this->url('internal.show_transactions_for_date', ['date' => '2020-12-01']),
                'pdf-family-transactions' => $this->url('internal.show_family_transactions', ['family' => $this->family->id]),
            ];
            foreach ($pages as $name => $url) {
                $this->scene($b, $name, function () use ($b, $url) {
                    $b->visit($url)->waitFor('.buttons-pdf');
                    $b->script('window.__pdfs = []; const original = URL.createObjectURL.bind(URL); URL.createObjectURL = blob => { const url = original(blob); window.__pdfs.push({url, type: blob.type}); return url; };');
                    $b->click('.buttons-pdf')->waitUsing(10, 200, fn () => $b->script('return window.__pdfs.filter(p => p.type === "application/pdf").length')[0] > 0);
                    $this->showPdf($b, 'window.__pdfs.find(p => p.type === "application/pdf").url');
                }, keepScroll: true);
            }

            $invoice = route('internal.show_child_family_invoice_pdf', ['year' => $this->year->id, 'family' => $this->family->id, 'child' => $this->child->id]);
            $this->scene($b, 'pdf-invoice', function () use ($b, $invoice) {
                $b->visit($this->url('internal.dashboard'))->waitFor('#top-navbar');
                $b->script('fetch('.json_encode($invoice).').then(r => r.blob()).then(blob => { window.__invoice = URL.createObjectURL(blob); });');
                $b->waitUsing(10, 200, fn () => $b->script('return !!window.__invoice')[0]);
                $this->showPdf($b, 'window.__invoice');
            }, keepScroll: true);
        });

        $this->assertNoVisualDifferences();
    }

    private function assertNoVisualDifferences(): void
    {
        $this->assertEmpty($this->failures, "Visual differences (see tests/Browser/screenshots/visual/diff):\n".implode("\n", $this->failures));
    }

    /** Opens a blob URL in the browser's PDF viewer (without toolbar and side panels). */
    private function showPdf(Browser $browser, string $urlExpression): void
    {
        $browser->script('location.href = '.$urlExpression.' + "#toolbar=0&navpanes=0&scrollbar=0&view=FitH";');
        $browser->pause(2500);
    }

    private function scene(Browser $browser, string $name, \Closure $setup, bool $keepScroll = false): void
    {
        try {
            $setup();
            $this->check($browser, $name, $keepScroll);
        } catch (\Throwable $e) {
            $browser->screenshot('visual/failed-'.$name);
            $this->failures[] = "$name: scene could not be set up: ".get_class($e).': '.strtok($e->getMessage(), "\n");
        }
    }

    private function check(Browser $browser, string $name, bool $keepScroll = false): void
    {
        // TODO: replace the fixed pauses and waitForText() calls in the scenes by waiting on explicit readiness markers.
        // Root causes: the pages render DataTables/registration rows asynchronously without a 'ready' signal (e.g. a
        // data-ready attribute on the table), and modal/tab transitions have no completion hook. A flaky scene (the
        // registration form's list rows) was caused by exactly this.
        if (!$keepScroll) {
            $browser->script('window.scrollTo(0, 0);');
        }
        $browser->pause(300)->script(<<<'JS'
            document.querySelectorAll('.container').forEach(e => { if (e.textContent.includes('Version info')) e.style.visibility = 'hidden'; });
            document.querySelectorAll('.today, .focused').forEach(e => e.classList.remove('today', 'focused'));
            document.querySelectorAll('input, textarea').forEach(e => { e.style.caretColor = 'transparent'; });
JS);
        $png = $browser->driver->takeScreenshot();
        $dir = __DIR__.'/screenshots/visual';
        $baseline = __DIR__.'/baselines/'.$name.'.png';
        @mkdir($dir.'/actual', 0777, true);
        @mkdir($dir.'/diff', 0777, true);
        @mkdir(dirname($baseline), 0777, true);
        file_put_contents("$dir/actual/$name.png", $png);

        if (getenv('UPDATE_VISUAL_BASELINES')) {
            file_put_contents($baseline, $png);

            return;
        }
        if (!file_exists($baseline)) {
            $this->failures[] = "$name: no baseline (run with UPDATE_VISUAL_BASELINES=1)";

            return;
        }
        $result = $browser->driver->executeAsyncScript($this->compareScript(), [base64_encode(file_get_contents($baseline)), base64_encode($png), self::CHANNEL_TOLERANCE, self::MAX_DIFFERENT_RATIO]);
        if (isset($result['error'])) {
            $this->failures[] = "$name: comparison failed: {$result['error']}";
        } elseif (isset($result['sizeMismatch'])) {
            $this->failures[] = "$name: size differs from baseline: ".json_encode($result['sizeMismatch']);
        } else {
            $ratio = $result['different'] / max(1, $result['total']);
            if (isset($result['diff'])) {
                file_put_contents("$dir/diff/$name.png", base64_decode($result['diff']));
                $this->failures[] = sprintf('%s: %.2f%% of the pixels differ', $name, $ratio * 100);
            }
        }
    }

    private function compareScript(): string
    {
        return <<<'JS'
            const args = arguments;
            const done = args[args.length - 1];
            const tolerance = args[2];
            const maxRatio = args[3];
            const load = b64 => new Promise((resolve, reject) => {
                const image = new Image();
                image.onload = () => resolve(image);
                image.onerror = () => reject(new Error('image failed to load'));
                image.src = 'data:image/png;base64,' + b64;
            });
            const pixels = image => {
                const canvas = document.createElement('canvas');
                canvas.width = image.width;
                canvas.height = image.height;
                const context = canvas.getContext('2d');
                context.drawImage(image, 0, 0);
                return context.getImageData(0, 0, image.width, image.height);
            };
            Promise.all([load(args[0]), load(args[1])]).then(([a, b]) => {
                if (a.width !== b.width || a.height !== b.height) {
                    return done({sizeMismatch: [a.width, a.height, b.width, b.height]});
                }
                const x = pixels(a), y = pixels(b), out = new ImageData(a.width, a.height);
                let different = 0;
                for (let i = 0; i < x.data.length; i += 4) {
                    const d = Math.max(Math.abs(x.data[i] - y.data[i]), Math.abs(x.data[i + 1] - y.data[i + 1]), Math.abs(x.data[i + 2] - y.data[i + 2]));
                    const isDifferent = d > tolerance;
                    if (isDifferent) different++;
                    out.data[i] = isDifferent ? 255 : y.data[i] * 0.3 + 178;
                    out.data[i + 1] = isDifferent ? 0 : y.data[i + 1] * 0.3 + 178;
                    out.data[i + 2] = isDifferent ? 0 : y.data[i + 2] * 0.3 + 178;
                    out.data[i + 3] = 255;
                }
                const total = a.width * a.height;
                if (different / total <= maxRatio) {
                    return done({different, total});
                }
                const canvas = document.createElement('canvas');
                canvas.width = a.width;
                canvas.height = a.height;
                canvas.getContext('2d').putImageData(out, 0, 0);
                done({different, total, diff: canvas.toDataURL('image/png').split(',')[1]});
            }).catch(e => done({error: String(e)}));
JS;
    }
}
