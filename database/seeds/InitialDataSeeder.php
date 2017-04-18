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
        $this->seed_child_families();
        $this->seed_activity_lists();
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
        $middle_group = \App\AgeGroup::where('abbreviation', '=', '6-12')->firstOrFail();
        $teenagers = \App\AgeGroup::where('abbreviation', '=', '12+')->firstOrFail();
        DB::table('children')->insert([
            'first_name' => 'Josephine',
            'last_name' => 'Janssens',
            'birth_year' => 2013,
            'age_group_id' => $kleuters->id,
            'remarks' => 'First kid in the DB!'
        ]);
        DB::table('children')->insert([
            'first_name' => 'Eefje',
            'last_name' => 'Janssens',
            'birth_year' => 2013,
            'age_group_id' => $kleuters->id,
            'remarks' => '!'
        ]);
        DB::table('children')->insert([
            'first_name' => 'Karen',
            'last_name' => 'Duyck',
            'birth_year' => 2008,
            'age_group_id' => $middle_group->id,
            'remarks' => ''
        ]);
        DB::table('children')->insert([
            'first_name' => 'Tom',
            'last_name' => 'Maes',
            'birth_year' => 2006,
            'age_group_id' => $teenagers->id,
            'remarks' => ''
        ]);
        DB::table('children')->insert([
            'first_name' => 'Erika',
            'last_name' => 'Van Leemhuyzen',
            'birth_year' => 2009,
            'age_group_id' => $middle_group->id,
            'remarks' => ''
        ]);
        DB::table('children')->insert([
            'first_name' => 'Tim',
            'last_name' => 'Beert',
            'birth_year' => 2010,
            'age_group_id' => $middle_group->id,
            'remarks' => ''
        ]);
    }

    private function seed_day_parts()
    {
        DB::table('day_parts')->insert([
            'name' => "Lunch",
            'order' => 1,
            'default' => true
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
        $week_day_ids = [];
        $week_day_names = ["Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrijdag"];
        $holidays = ["2017-07-21"];
        for ($i = 0; $i < 5; ++$i) {
            $week_day_ids[] = DB::table('week_days')->insertGetId([
                'days_offset' => $i,
                'name' => $week_day_names[$i]
            ]);
        }
        $monday = (new DateTimeImmutable())->setDate(2017, 7, 3);
        $day = new DateInterval('P1D');
        $week = new DateInterval('P1W');
        for ($i = 0; $i < 6; ++$i) {
            $week_id = DB::table('weeks')->insertGetId([
                'year_id' => $year_2017_id,
                'week_number' => 1 + $i,
                'first_day_of_week' => $monday
            ]);
            $week_day = $monday;
            for ($j = 0; $j < count($week_day_ids); ++$j) {
                if (!in_array($week_day->format("Y-m-d"), $holidays)) {
                    DB::table('playground_days')->insert([
                        'week_id' => $week_id,
                        'week_day_id' => $week_day_ids[$j]
                    ]);
                    $week_day = $week_day->add($day);
                }
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

    private function seed_child_families()
    {
        $normal_tariff = \App\Tariff::where('name', '=', 'Normaal')->firstOrFail();
        $social_tariff = \App\Tariff::where('name', '=', 'Sociaal')->firstOrFail();
        $family_id = DB::table('families')->insertGetId([
            "guardian_first_name" => "Jozef",
            "guardian_last_name" => "De Backer",
            "tariff_id" => $normal_tariff->id,
            "remarks" => "First family",
            "contact" => "1207"
        ]);
        $child = \App\Child::where('first_name', '=', 'Josephine')->firstOrFail();
        DB::table('child_families')->insert([
            "family_id" => $family_id,
            "child_id" => $child->id
        ]);
        $child = \App\Child::where('first_name', '=', 'Eefje')->firstOrFail();
        DB::table('child_families')->insert([
            "family_id" => $family_id,
            "child_id" => $child->id
        ]);
        DB::table('families')->insert([
            "guardian_first_name" => "Heidi",
            "guardian_last_name" => "De Vriendt",
            "tariff_id" => $normal_tariff->id,
            "remarks" => "Second family",
            "contact" => "999"
        ]);
        DB::table('families')->insert([
            "guardian_first_name" => "Jonas",
            "guardian_last_name" => "De Keukeleire",
            "tariff_id" => $normal_tariff->id,
            "remarks" => "",
            "contact" => "112"
        ]);
        DB::table('families')->insert([
            "guardian_first_name" => "Erik",
            "guardian_last_name" => "Anthonissen",
            "tariff_id" => $normal_tariff->id,
            "remarks" => "",
            "contact" => ""
        ]);
        DB::table('families')->insert([
            "guardian_first_name" => "Lieve",
            "guardian_last_name" => "Bouckaert",
            "tariff_id" => $normal_tariff->id,
            "remarks" => "",
            "contact" => ""
        ]);
        DB::table('families')->insert([
            "guardian_first_name" => "Ann",
            "guardian_last_name" => "Meert",
            "tariff_id" => $social_tariff->id,
            "remarks" => "",
            "contact" => ""
        ]);
    }

    private function seed_activity_lists()
    {
        DB::table('activity_lists')->insert([
            "name" => "Zwembad O'Town",
            "date" => "2017-07-06",
            "show_on_attendance_form" => true,
            "show_on_dashboard" => true,
            "price" => "1.75"
        ]);
    }

}
