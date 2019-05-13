<?php

use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    protected $organization;
    protected $year;
    protected $week_ids = [];
    protected $week_day_ids = [];
    protected $toddlers_id;
    protected $middle_group_id;
    protected $teenagers_id;
    protected $home_id;
    protected $whole_day_id;
    protected $first_admin_session_id;

    /**
     * Run the database seeds.
     *
     * @throws Exception
     */
    public function run()
    {
        $this->seed_organization_and_year();
        $this->seed_admin_sessions();
        $this->seed_age_groups();
        $this->seed_day_parts();
        $this->seed_supplements();
        $this->seed_dates();
        $this->seed_tariffs();
    }

    public function weeks($week_id)
    {
        return \App\Week::findOrFail($this->week_ids[$week_id]);
    }

    public function week_days($week_day_id)
    {
        return \App\WeekDay::findOrFail($this->week_day_ids[$week_day_id]);
    }

    protected function seed_organization_and_year()
    {
        $this->organization = \App\Organization::create(['full_name' => 'Jokkebrok']);
        $this->year = \App\Year::create([
            'organization_id' => $this->organization->id,
            'description' => '2018',
            'title' => '2018',
        ]);
    }

    protected function seed_admin_sessions()
    {
        $this->first_admin_session_id = \App\AdminSession::create(['year_id' => $this->year->id, 'responsible_name' => 'Dummy'])->id;
    }

    protected function seed_age_groups()
    {
        $this->toddlers_id = \App\AgeGroup::create(
            [
                'year_id' => $this->year->id,
                'name' => 'Kleuters',
                'abbreviation' => 'KLS',
                'start_date' => (new DateTime())->setDate(2012, 1, 1),
                'end_date' => (new DateTime())->setDate(2015, 1, 1), ]
        )->id;
        $this->middle_group_id = \App\AgeGroup::create(
            [
                'year_id' => $this->year->id,
                'name' => 'Grote',
                'abbreviation' => '6-12',
                'start_date' => (new DateTime())->setDate(2005, 1, 1),
                'end_date' => (new DateTime())->setDate(2012, 1, 1), ]
        )->id;
        $this->teenagers_id = \App\AgeGroup::create(
            [
                'year_id' => $this->year->id,
                'name' => 'Tieners',
                'abbreviation' => '12+',
                'start_date' => (new DateTime())->setDate(2003, 1, 1),
                'end_date' => (new DateTime())->setDate(2005, 1, 1), ]
        )->id;
    }

    protected function seed_day_parts()
    {
        $this->whole_day_id = \App\DayPart::create([
            'year_id' => $this->year->id,
            'name' => 'Lunch',
            'order' => 1,
            'default' => true,
        ])->id;
        $this->home_id = \App\DayPart::create([
            'year_id' => $this->year->id,
            'name' => 'Thuis',
            'order' => 2,
        ])->id;
    }

    protected function seed_supplements()
    {
        factory(\App\Supplement::class)->create([
            'year_id' => $this->year->id,
            'name' => 'IJsje',
            'price' => '0.50',
        ]);
    }

    /**
     * @throws Exception
     */
    protected function seed_dates()
    {
        $week_day_names = ['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag'];
        $holidays = ['2018-07-21'];
        for ($i = 0; $i < 5; ++$i) {
            $this->week_day_ids[] = \App\WeekDay::create([
                'year_id' => $this->year->id,
                'days_offset' => $i,
                'name' => $week_day_names[$i],
            ])->id;
        }
        $monday = (new DateTimeImmutable())->setDate(2018, 7, 2);
        $day = new DateInterval('P01D');
        $week = new DateInterval('P1W');
        for ($i = 0; $i < 6; ++$i) {
            $this->week_ids[$i] = \App\Week::create([
                'year_id' => $this->year->id,
                'week_number' => 1 + $i,
                'first_day_of_week' => $monday->format('Y-m-d'),
            ])->id;
            $week_day = $monday;
            for ($j = 0; $j < count($this->week_day_ids); ++$j) {
                if (!in_array($week_day->format('Y-m-d'), $holidays)) {
                    \App\PlaygroundDay::create([
                        'year_id' => $this->year->id,
                        'week_id' => $this->week_ids[$i],
                        'week_day_id' => $this->week_day_ids[$j],
                    ]);
                    $week_day = $week_day->add($day);
                }
            }
            $monday = $monday->add($week);
        }
    }

    protected function seed_tariffs()
    {
        \App\Tariff::create([
            'year_id' => $this->year->id,
            'name' => 'Normaal',
            'abbreviation' => 'NRML',
            'day_first_child' => 5.00,
            'day_later_children' => 4.00,
            'week_first_child' => 22.5,
            'week_later_children' => 18.5,
        ]);
        \App\Tariff::create([
            'year_id' => $this->year->id,
            'name' => 'Sociaal',
            'abbreviation' => 'SCL',
            'day_first_child' => 2.5,
            'day_later_children' => 2,
            'week_first_child' => 12,
            'week_later_children' => 9.5,
        ]);
    }
}
