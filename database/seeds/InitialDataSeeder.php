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
        $this->seed_children();
        $this->seed_day_parts();
        $this->seed_supplements();
        $this->seed_dates();
        $this->seed_tariffs();
        $this->seed_child_families();
        $this->seed_activity_lists();
        $this->seed_registrations();
    }

    private $week_ids = [];
    private $week_day_ids = [];
    private $family_ids = [];
    private $child_ids = [];
    private $family_week_registration_ids = [];
    private $child_family_week_registration_ids = [];
    private $child_family_day_registration_ids = [];
    private $toddlers_id = null;
    private $middle_group_id = null;
    private $teenagers_id = null;
    private $home_id;
    private $whole_day_id;
    private $first_admin_session_id;

    private function families($family_id)
    {
        return \App\Family::findOrFail($this->family_ids[$family_id]);
    }

    private function children($child_id)
    {
        return \App\Child::findOrFail($this->child_ids[$child_id]);
    }

    private function weeks($week_id)
    {
        return \App\Week::findOrFail($this->week_ids[$week_id]);
    }

    private function week_days($week_day_id)
    {
        return \App\WeekDay::findOrFail($this->week_day_ids[$week_day_id]);
    }

    private function seed_admin_sessions()
    {
        $this->first_admin_session_id = DB::table('admin_sessions')->insertGetId([]);
    }

    private function seed_age_groups()
    {
        $this->toddlers_id = DB::table('age_groups')->insert(['name' => 'Kleuters',
            'abbreviation' => 'KLS',
            'start_date' => (new DateTime())->setDate(2011, 1, 1),
            'end_date' => (new DateTime())->setDate(2014, 1, 1)]);
        $this->middle_group_id = DB::table('age_groups')->insert(['name' => 'Grote',
            'abbreviation' => '6-12',
            'start_date' => (new DateTime())->setDate(2006, 1, 1),
            'end_date' => (new DateTime())->setDate(2011, 1, 1)]);
        $this->teenagers_id = DB::table('age_groups')->insert(['name' => 'Tieners',
            'abbreviation' => '12+',
            'start_date' => (new DateTime())->setDate(2004, 1, 1),
            'end_date' => (new DateTime())->setDate(2006, 1, 1)]);
    }

    private function seed_children()
    {
        $this->child_ids['josephine_janssens'] = DB::table('children')->insertGetId([
            'first_name' => 'Josephine',
            'last_name' => 'Janssens',
            'birth_year' => 2013,
            'age_group_id' => $this->toddlers_id,
            'remarks' => 'First kid in the DB!'
        ]);
        $this->child_ids['eefje_janssens'] = DB::table('children')->insertGetId([
            'first_name' => 'Eefje',
            'last_name' => 'Janssens',
            'birth_year' => 2013,
            'age_group_id' => $this->toddlers_id,
            'remarks' => '!'
        ]);
        $this->child_ids['karen_duyck'] = DB::table('children')->insertGetId([
            'first_name' => 'Karen',
            'last_name' => 'Duyck',
            'birth_year' => 2008,
            'age_group_id' => $this->middle_group_id,
            'remarks' => ''
        ]);
        $this->child_ids['tom_maes'] = DB::table('children')->insertGetId([
            'first_name' => 'Tom',
            'last_name' => 'Maes',
            'birth_year' => 2006,
            'age_group_id' => $this->teenagers_id,
            'remarks' => ''
        ]);
        $this->child_ids['erika_van_leemhuyzen'] = DB::table('children')->insertGetId([
            'first_name' => 'Erika',
            'last_name' => 'Van Leemhuyzen',
            'birth_year' => 2009,
            'age_group_id' => $this->middle_group_id,
            'remarks' => ''
        ]);
        $this->child_ids['tim_beert'] = DB::table('children')->insertGetId([
            'first_name' => 'Tim',
            'last_name' => 'Beert',
            'birth_year' => 2010,
            'age_group_id' => $this->middle_group_id,
            'remarks' => ''
        ]);
        $this->child_ids['tom_beert'] = DB::table('children')->insertGetId([
            'first_name' => 'Tom',
            'last_name' => 'Beert',
            'birth_year' => 2011,
            'age_group_id' => $this->middle_group_id,
            'remarks' => ''
        ]);
        $this->child_ids['jonas_beert'] = DB::table('children')->insertGetId([
            'first_name' => 'Jonas',
            'last_name' => 'Beert',
            'birth_year' => 2007,
            'age_group_id' => $this->middle_group_id,
            'remarks' => ''
        ]);
        $this->child_ids['lieven_devriendt'] = DB::table('children')->insertGetId([
            'first_name' => 'Lieven',
            'last_name' => 'Devriendt',
            'birth_year' => 2006,
            'age_group_id' => $this->middle_group_id,
            'remarks' => ''
        ]);
        $this->child_ids['ann_vanovertveldt'] = DB::table('children')->insertGetId([
            'first_name' => 'Ann',
            'last_name' => 'Vanovertveldt',
            'birth_year' => 2011,
            'age_group_id' => $this->middle_group_id,
            'remarks' => ''
        ]);
        $this->child_ids['johan_claeys'] = DB::table('children')->insertGetId([
            'first_name' => 'Johan',
            'last_name' => 'Claeys',
            'birth_year' => 2013,
            'age_group_id' => $this->toddlers_id,
            'remarks' => ''
        ]);
        $this->child_ids['driss_de_koninck'] = DB::table('children')->insertGetId([
            'first_name' => 'Driss',
            'last_name' => 'De Koninck',
            'birth_year' => 2012,
            'age_group_id' => $this->toddlers_id,
            'remarks' => ''
        ]);
        $this->child_ids['veerle_christy'] = DB::table('children')->insertGetId([
            'first_name' => 'Veerle',
            'last_name' => 'Christy',
            'birth_year' => 2010,
            'age_group_id' => $this->middle_group_id,
            'remarks' => ''
        ]);
    }

    private function seed_day_parts()
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
        $this->family_ids['jozef_de_backer'] = DB::table('families')->insertGetId([
            "guardian_first_name" => "Jozef",
            "guardian_last_name" => "De Backer",
            "tariff_id" => $normal_tariff->id,
            "remarks" => "First family",
            "contact" => "1207"
        ]);
        $child = \App\Child::where('first_name', '=', 'Josephine')->firstOrFail();
        DB::table('child_families')->insert([
            "family_id" => $this->family_ids['jozef_de_backer'],
            "child_id" => $child->id
        ]);
        $child = \App\Child::where('first_name', '=', 'Eefje')->firstOrFail();
        DB::table('child_families')->insert([
            "family_id" => $this->family_ids['jozef_de_backer'],
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

    private function seed_registrations()
    {
        $family_jozef_de_backer = $this->families('jozef_de_backer');
        $week0 = $this->weeks(0);
        $this->family_week_registration_ids['jozef_de_backer_week_0'] = DB::table('family_week_registrations')->insert([
            "family_id" => $family_jozef_de_backer->id,
            "week_id" => $week0->id,
            "tariff_id" => $family_jozef_de_backer->tariff_id
        ]);
        $child_josephine_janssens = $this->children('josephine_janssens');
        $this->child_family_week_registration_ids['josephine_janssens_week0'] = DB::table('child_family_week_registrations')->insert([
            "child_id" => $child_josephine_janssens->id,
            "family_id" => $family_jozef_de_backer->id,
            "week_id" => $week0->id,
            "whole_week_price" => false
        ]);
        $week_day0 = $this->week_days(0);
        $this->child_family_day_registration_ids['josephine_janssens_week0_day0'] = DB::table('child_family_day_registrations')->insert([
            "child_id" => $child_josephine_janssens->id,
            "family_id" => $family_jozef_de_backer->id,
            "week_id" => $week0->id,
            "week_day_id" => $week_day0->id,
            "day_part_id" => $this->whole_day_id,
            "attended" => true,
            "age_group_id" => $this->toddlers_id
        ]);
        $week_day1 = $this->week_days(1);
        $this->child_family_day_registration_ids['josephine_janssens_week0_day1'] = DB::table('child_family_day_registrations')->insert([
            "child_id" => $child_josephine_janssens->id,
            "family_id" => $family_jozef_de_backer->id,
            "week_id" => $week0->id,
            "week_day_id" => $week_day1->id,
            "day_part_id" => $this->whole_day_id,
            "attended" => true,
            "age_group_id" => $this->toddlers_id
        ]);
        $child_eefje_janssens = $this->children('eefje_janssens');
        $this->child_family_week_registration_ids['eefje_janssens_week0'] = DB::table('child_family_week_registrations')->insert([
            "child_id" => $child_eefje_janssens->id,
            "family_id" => $family_jozef_de_backer->id,
            "week_id" => $week0->id,
            "whole_week_price" => true
        ]);
        foreach ($week0->playground_days as $playground_day) {
            $this->child_family_day_registration_ids['eefje_janssens_week0_day' . $playground_day->week_day->days_offset] = DB::table('child_family_day_registrations')->insert([
                "child_id" => $child_eefje_janssens->id,
                "family_id" => $family_jozef_de_backer->id,
                "week_id" => $week0->id,
                "week_day_id" => $playground_day->week_day_id,
                "day_part_id" => $this->whole_day_id,
                "attended" => false,
                "age_group_id" => $this->toddlers_id
            ]);
        }

        DB::table('transactions')->insertGetId([
            'amount_paid' => 30.5,
            'amount_expected' => 30.5,
            'family_id' => $family_jozef_de_backer->id,
            'admin_session_id' => $this->first_admin_session_id,
            'created_at' => \Carbon\Carbon::now()
        ]);
    }

}
