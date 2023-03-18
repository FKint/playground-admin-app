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
        $new_year = $this->year->make_copy(
            $this->other_organization->id,
            '2019',
            'Jokkebrok 2',
            \Carbon\CarbonImmutable::createFromFormat('Y-m-d', '2018-04-01'),
            \Carbon\CarbonImmutable::createFromFormat('Y-m-d', '2018-04-15'),
            []
        );
        $this->assertEquals($this->other_organization->id, $new_year->organization_id);
        $this->assertEquals('2019', $new_year->title);
        $this->assertEquals('Jokkebrok 2', $new_year->description);
        $this->assertEquals($this->year->invoice_header_text, $new_year->invoice_header_text);
        $this->assertEquals($this->year->invoice_header_image, $new_year->invoice_header_image);
        $this->assertEquals($this->year->invoice_bank_account, $new_year->invoice_bank_account);
        $this->assertEquals(10, $new_year->playground_days()->count());
        $this->assertEquals($this->year->supplements()->count(), $new_year->supplements()->count());
        $this->assertEquals($this->year->age_groups()->count(), $new_year->age_groups()->count());
        $this->assertEquals($this->year->day_parts()->count(), $new_year->day_parts()->count());
        $this->assertEquals($this->year->tariffs()->count(), $new_year->tariffs()->count());
        $this->assertEquals($this->year->week_days()->count(), $new_year->week_days()->count());
        $this->assertTrue($new_year->playground_days()->firstOrFail()->date()->isSameDay(\Carbon\CarbonImmutable::create(2018, 4, 2)));
    }
}
