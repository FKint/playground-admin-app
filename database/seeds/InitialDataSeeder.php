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
        $this->seed_age_groups();
        $this->seed_children();
        $this->seed_day_parts();
        $this->seed_supplements();
        $this->seed_dates();
    }

    private function seed_age_groups()
    {
        DB::table('age_groups')->insert(['name' => 'Kleuters',
            'abbreviation' => 'KLS',
            'start_date' => (new DateTime())->setDate(2011, 1, 1),
            'end_date' => (new DateTime())->setDate(2014, 1, 1)]);
        DB::table('age_groups')->insert(['name' => 'Grote',
            'abbreviation' => '6-12',
            'start_date' => (new DateTime())->setDate(2006, 1, 1),
            'end_date' => (new DateTime())->setDate(2011, 1, 1)]);
        DB::table('age_groups')->insert(['name' => 'Tieners',
            'abbreviation' => '12+',
            'start_date' => (new DateTime())->setDate(2004, 1, 1),
            'end_date' => (new DateTime())->setDate(2006, 1, 1)]);
    }

    private function seed_children()
    {
        $kleuters = \App\AgeGroup::where('abbreviation', '=', 'KLS')->firstOrFail();
        DB::table('children')->insert([
            'first_name' => 'Josephine',
            'last_name' => 'Janssens',
            'birth_year' => 2013,
            'age_group_id' => $kleuters->id,
            'remarks' => 'First kid in the DB!'
        ]);
    }

    private function seed_day_parts()
    {
        DB::table('day_parts')->insert([
            'name' => "Lunch",
        ]);
        DB::table('day_parts')->insert([
            'name' => "Thuis"
        ]);
    }

    private function seed_supplements()
    {
        DB::table('supplements')->insert([
            'name' => "IJsje",
            'price' => '0.50'
        ]);
    }

    private function seed_dates()
    {
        $year_2017_id = DB::table('years')->insertGetId([
            'year' => 2017
        ]);
        $monday = (new DateTimeImmutable())->setDate(2017, 7, 3);
        $day = new DateInterval('P1D');
        $week = new DateInterval('P1W');
        for ($i = 0; $i < 6; ++$i) {
            $week_id = DB::table('weeks')->insertGetId([
                'year_id' => $year_2017_id,
                'week_number' => 1 + $i
            ]);
            $week_day = $monday;
            for ($j = 0; $j < 5; ++$j) {
                DB::table('days')->insert([
                    'week_id' => $week_id,
                    'date' => $week_day
                ]);
                $week_day = $week_day->add($day);
            }
            $monday = $monday->add($week);
        }

    }
}
