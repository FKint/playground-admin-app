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
        $this->seed_tariffs();
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
            'order' => 1
        ]);
        DB::table('day_parts')->insert([
            'name' => "Thuis",
            'order' => 2
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

    private function seed_tariffs()
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
