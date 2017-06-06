<?php

use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seed_admin_sessions();
        $this->seed_age_groups();
        $this->seed_day_parts();
        $this->seed_supplements();
        $this->seed_dates();
        $this->seed_tariffs();
    }

    protected $week_ids = [];
    protected $week_day_ids = [];
    protected $toddlers_id = null;
    protected $middle_group_id = null;
    protected $teenagers_id = null;
    protected $home_id;
    protected $whole_day_id;
    protected $first_admin_session_id;

    public function weeks($week_id)
    {
        return \App\Week::findOrFail($this->week_ids[$week_id]);
    }

    public function week_days($week_day_id)
    {
        return \App\WeekDay::findOrFail($this->week_day_ids[$week_day_id]);
    }

    protected function seed_admin_sessions()
    {
        $this->first_admin_session_id = DB::table('admin_sessions')->insertGetId([]);
    }

    protected function seed_age_groups()
    {
        $this->toddlers_id = DB::table('age_groups')->insert(['name' => 'Kleuters',
            'abbreviation' => 'KLS',
            'start_date' => (new DateTime())->setDate(2012, 1, 1),
            'end_date' => (new DateTime())->setDate(2015, 1, 1)]);
        $this->middle_group_id = DB::table('age_groups')->insert(['name' => 'Grote',
            'abbreviation' => '6-12',
            'start_date' => (new DateTime())->setDate(2005, 1, 1),
            'end_date' => (new DateTime())->setDate(2012, 1, 1)]);
        $this->teenagers_id = DB::table('age_groups')->insert(['name' => 'Tieners',
            'abbreviation' => '12+',
            'start_date' => (new DateTime())->setDate(2003, 1, 1),
            'end_date' => (new DateTime())->setDate(2005, 1, 1)]);
    }

    protected function seed_day_parts()
    {
        $this->whole_day_id = DB::table('day_parts')->insertGetId([
            'name' => "Lunch",
            'order' => 1,
            'default' => true
        ]);
        $this->home_id = DB::table('day_parts')->insertGetId([
            'name' => "Thuis",
            'order' => 2
        ]);
    }

    protected function seed_supplements()
    {
        DB::table('supplements')->insert([
            'name' => "IJsje",
            'price' => '0.50'
        ]);
    }

    protected function seed_dates()
    {
        $year_2017_id = DB::table('years')->insertGetId([
            'year' => 2017
        ]);
        $week_day_names = ["Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrijdag"];
        $holidays = ["2017-07-21"];
        for ($i = 0; $i < 5; ++$i) {
            $this->week_day_ids[] = DB::table('week_days')->insertGetId([
                'days_offset' => $i,
                'name' => $week_day_names[$i]
            ]);
        }
        $monday = (new DateTimeImmutable())->setDate(2017, 7, 3);
        $day = new DateInterval('P1D');
        $week = new DateInterval('P1W');
        for ($i = 0; $i < 6; ++$i) {
            $this->week_ids[$i] = DB::table('weeks')->insertGetId([
                'year_id' => $year_2017_id,
                'week_number' => 1 + $i,
                'first_day_of_week' => $monday
            ]);
            $week_day = $monday;
            for ($j = 0; $j < count($this->week_day_ids); ++$j) {
                if (!in_array($week_day->format("Y-m-d"), $holidays)) {
                    DB::table('playground_days')->insert([
                        'week_id' => $this->week_ids[$i],
                        'week_day_id' => $this->week_day_ids[$j]
                    ]);
                    $week_day = $week_day->add($day);
                }
            }
            $monday = $monday->add($week);
        }
    }

    protected function seed_tariffs()
    {
        DB::table('tariffs')->insert([
            "name" => "Normaal",
            "abbreviation" => "NRML",
            "day_first_child" => 5.00,
            "day_later_children" => 4.00,
            "week_first_child" => 22.5,
            "week_later_children" => 18.5
        ]);
        DB::table('tariffs')->insert([
            "name" => "Sociaal",
            "abbreviation" => "SCL",
            "day_first_child" => 2.5,
            "day_later_children" => 2,
            "week_first_child" => 12,
            "week_later_children" => 9.5
        ]);
    }

}
