<?php

namespace Tests\Unit;

use App\ActivityList;
use App\AdminSession;
use App\AgeGroup;
use App\DayPart;
use App\Family;
use App\Supplement;
use App\Tariff;
use App\Week;
use App\WeekDay;
use App\Year;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CloneYearTest extends TestCase
{
    protected $year;

    public function setUp(): void
    {
        parent::setUp();
        $this->year = factory(Year::class)->create();
        factory(Family::class)->create(['year_id' => $this->year->id]);
        factory(ActivityList::class)->create(['year_id' => $this->year->id]);
        factory(Tariff::class, 2)->create(['year_id' => $this->year->id]);
        factory(AgeGroup::class)->create(['year_id' => $this->year->id]);
        $weekDays = array_map(
            function ($offset) {
                return factory(WeekDay::class)->create(['year_id' => $this->year->id, 'days_offset' => $offset]);
            },
            [0, 1, 2, 3, 4]
        );
        $actualWeeks = array_map(function ($o) {
            return Week::findOrFail($o['id']);
        }, factory(Week::class, 8)->create(['year_id' => $this->year->id])->toArray());

        factory(DayPart::class)->create([
            'default' => true,
            'year_id' => $this->year->id,
        ]);
        factory(AdminSession::class)->create([
            'year_id' => $this->year->id,
        ]);
        factory(Supplement::class)->create([
            'year_id' => $this->year->id,
        ]);
    }

    public function testCloneYear()
    {
        $new_year = $this->year->make_copy(
            'Jokkebrok 2',
            \DateTimeImmutable::createFromFormat('Y-m-d', '2018-04-01'),
            \DateTimeImmutable::createFromFormat('Y-m-d', '2018-04-15'),
            []
        );
        $this->assertEquals(10, $new_year->playground_days()->count());
        $this->assertEquals($this->year->supplements()->count(), $new_year->supplements()->count());
        $this->assertEquals($this->year->age_groups()->count(), $new_year->age_groups()->count());
        $this->assertEquals($this->year->day_parts()->count(), $new_year->day_parts()->count());
        $this->assertEquals($this->year->tariffs()->count(), $new_year->tariffs()->count());
        $this->assertEquals($this->year->week_days()->count(), $new_year->week_days()->count());
    }
}
