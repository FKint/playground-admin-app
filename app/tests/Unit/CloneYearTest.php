<?php

namespace Tests\Unit;

use App\ActivityList;
use App\AdminSession;
use App\AgeGroup;
use App\DayPart;
use App\Family;
use App\Organization;
use App\Supplement;
use App\Tariff;
use App\Week;
use App\WeekDay;
use App\Year;
use Carbon\CarbonImmutable;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CloneYearTest extends TestCase
{
    protected $year;
    protected $other_organization;

    public function setUp(): void
    {
        parent::setUp();
        $this->year = Year::factory()->create();
        $this->other_organization = Organization::factory()->create();
        ActivityList::factory()->for($this->year)->create();
        $tariffs = Tariff::factory()->count(2)->for($this->year)->create();
        AgeGroup::factory()->for($this->year)->create();
        array_map(
            function ($offset) {
                return WeekDay::factory()->for($this->year)->create(['days_offset' => $offset]);
            },
            [0, 1, 2, 3, 4]
        );
        Week::factory()->count(8)->for($this->year)->create();
        DayPart::factory()->for($this->year)->create([
            'default' => true,
        ]);
        AdminSession::factory()->for($this->year)->create();
        Supplement::factory()->for($this->year)->create();
        Family::factory()->for($this->year)->for($tariffs[0])->create();
    }

    public function testCloneYear()
    {
        $highest_id = Year::all()->max('id');
        $new_id = $highest_id + 1;
        $this->artisan('year:clone '.$this->year->id)
            ->expectsQuestion('Organization?', $this->other_organization->id)
            ->expectsQuestion('Year title?', 'Year 2023')
            ->expectsQuestion('Year description?', 'Jokkebrok 2')
            ->expectsQuestion('[First day] Enter a date (yyyy-mm-dd)', '2023-07-03')
            ->expectsQuestion('[Last day] Enter a date (yyyy-mm-dd)', '2023-08-11')
            ->expectsQuestion('Do you want to add an exception day (remove a date from the calendar)? [yn]', 'y')
            ->expectsQuestion('[Exception day] Enter a date (yyyy-mm-dd)', '2023-07-21')
            ->expectsQuestion('Do you want to add an exception day (remove a date from the calendar)? [yn]', 'n')
            ->expectsOutput('Year cloned. New year id: '.$new_id)
            ->assertExitCode(0);
        $new_year = Year::find($new_id);

        $this->assertEquals($this->other_organization->id, $new_year->organization_id);
        $this->assertEquals('Year 2023', $new_year->title);
        $this->assertEquals('Jokkebrok 2', $new_year->description);
        $this->assertEquals($this->year->invoice_header_text, $new_year->invoice_header_text);
        $this->assertEquals($this->year->invoice_header_image, $new_year->invoice_header_image);
        $this->assertEquals($this->year->invoice_bank_account, $new_year->invoice_bank_account);
        $this->assertEquals(29, $new_year->playground_days()->count());
        $this->assertTrue($new_year->playground_days()->firstOrFail()->date()->isSameDay(CarbonImmutable::create(2023, 7, 3)));
        $all_playground_days_dates = array_map(fn ($d): CarbonImmutable => $d['date'], $new_year->playground_days()->cursor()->toArray());
        $this->assertTrue($this->arrayContainsDate(CarbonImmutable::create(2023, 7, 20), $all_playground_days_dates), '2023-07-20 is a playground day');
        $this->assertFalse($this->arrayContainsDate(CarbonImmutable::create(2023, 4, 21), $all_playground_days_dates), '2023-07-21 is not a playground day');
        $this->assertEquals($this->year->supplements()->count(), $new_year->supplements()->count());
        $this->assertEquals($this->year->age_groups()->count(), $new_year->age_groups()->count());
        $this->assertEquals($this->year->day_parts()->count(), $new_year->day_parts()->count());
        $this->assertEquals($this->year->tariffs()->count(), $new_year->tariffs()->count());
        $this->assertEquals($this->year->week_days()->count(), $new_year->week_days()->count());
    }

    /**
     * @param CarbonImmutable[] $haystack
     */
    private function arrayContainsDate(CarbonImmutable $needle, array $haystack)
    {
        return count(array_filter($haystack, fn ($d): bool => $d->eq($needle))) > 0;
    }
}
