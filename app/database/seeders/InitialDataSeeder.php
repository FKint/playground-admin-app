<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DateTime;
use DateTimeImmutable;
use DateInterval;

class InitialDataSeeder extends Seeder
{
    protected $organization;
    protected $year;
    protected $weeks = [];
    protected $week_days = [];
    protected $toddlers;
    protected $middle_group;
    protected $teenagers;
    protected $home;
    protected $whole_day;
    protected $first_admin_session;

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
        return \App\Week::findOrFail($week_id);
    }

    public function week_days($week_day_id)
    {
        return \App\WeekDay::findOrFail($week_day_id);
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
        $this->first_admin_session = \App\AdminSession::create(['year_id' => $this->year->id, 'responsible_name' => 'Dummy']);
    }

    protected function seed_age_groups()
    {
        $this->toddlers = \App\AgeGroup::create(
            [
                'year_id' => $this->year->id,
                'name' => 'Kleuters',
                'abbreviation' => 'KLS',
                'start_date' => (new DateTime())->setDate(2012, 1, 1),
                'end_date' => (new DateTime())->setDate(2015, 1, 1), ]
        );
        $this->middle_group = \App\AgeGroup::create(
            [
                'year_id' => $this->year->id,
                'name' => 'Grote',
                'abbreviation' => '6-12',
                'start_date' => (new DateTime())->setDate(2005, 1, 1),
                'end_date' => (new DateTime())->setDate(2012, 1, 1), ]
            );
        $this->teenagers = \App\AgeGroup::create(
            [
                'year_id' => $this->year->id,
                'name' => 'Tieners',
                'abbreviation' => '12+',
                'start_date' => (new DateTime())->setDate(2003, 1, 1),
                'end_date' => (new DateTime())->setDate(2005, 1, 1), ]
        );
    }

    protected function seed_day_parts()
    {
        $this->whole_day = \App\DayPart::create([
            'year_id' => $this->year->id,
            'name' => 'Lunch',
            'order' => 1,
            'default' => true,
        ]);
        $this->home = \App\DayPart::create([
            'year_id' => $this->year->id,
            'name' => 'Thuis',
            'order' => 2,
        ]);
    }

    protected function seed_supplements()
    {
        \App\Supplement::factory()->for($this->year)->create([
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
            $this->week_days[] = \App\WeekDay::create([
                'year_id' => $this->year->id,
                'days_offset' => $i,
                'name' => $week_day_names[$i],
            ]);
        }
        $monday = (new DateTimeImmutable())->setDate(2018, 7, 2);
        $day = new DateInterval('P01D');
        $week = new DateInterval('P1W');
        for ($i = 0; $i < 6; ++$i) {
            $this->weeks[$i] = \App\Week::create([
                'year_id' => $this->year->id,
                'week_number' => 1 + $i,
                'first_day_of_week' => $monday->format('Y-m-d'),
            ]);
            $week_day = $monday;
            for ($j = 0; $j < count($this->week_days); ++$j) {
                if (!in_array($week_day->format('Y-m-d'), $holidays)) {
                    \App\PlaygroundDay::create([
                        'year_id' => $this->year->id,
                        'week_id' => $this->weeks[$i]->id,
                        'week_day_id' => $this->week_days[$j]->id,
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
