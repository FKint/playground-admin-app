<?php

use Illuminate\Database\Seeder;

class DummyDataSeeder extends InitialDataSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        parent::run();
        $this->seed_children();
        $this->seed_child_families();
        $this->seed_activity_lists();
        $this->seed_registrations();
    }

    protected $family_ids = [];
    protected $child_ids = [];
    protected $family_week_registration_ids = [];
    protected $child_family_ids = [];
    protected $child_family_week_registration_ids = [];
    protected $child_family_day_registration_ids = [];
    protected $activity_list_id;


    protected function families($family_id)
    {
        return \App\Family::findOrFail($this->family_ids[$family_id]);
    }

    protected function children($child_id)
    {
        return \App\Child::findOrFail($this->child_ids[$child_id]);
    }

    protected function child_families($child_family_id)
    {
        return \App\ChildFamily::findOrFail($this->child_family_ids[$child_family_id]);
    }

    protected function seed_children()
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


    protected function seed_child_families()
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
        $this->child_family_ids['jozef_de_backer_josephine'] = DB::table('child_families')->insertGetId([
            "family_id" => $this->family_ids['jozef_de_backer'],
            "child_id" => $child->id
        ]);
        $child = \App\Child::where('first_name', '=', 'Eefje')->firstOrFail();
        $this->child_family_ids['jozef_de_backer_eefje'] = DB::table('child_families')->insertGetId([
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

    protected function seed_activity_lists()
    {
        $this->activity_list_id = DB::table('activity_lists')->insert([
            "name" => "Zwembad O'Town",
            "date" => "2017-07-06",
            "show_on_attendance_form" => true,
            "show_on_dashboard" => true,
            "price" => "1.75"
        ]);
        DB::table('activity_list_child_families')->insert([
            "child_family_id" => $this->child_families('jozef_de_backer_josephine')->id,
            "activity_list_id" => $this->activity_list_id
        ]);
        DB::table('activity_list_child_families')->insert([
            "child_family_id" => $this->child_families('jozef_de_backer_eefje')->id,
            "activity_list_id" => $this->activity_list_id
        ]);
    }

    protected function seed_registrations()
    {
        $family_jozef_de_backer = $this->families('jozef_de_backer');
        $week0 = $this->weeks(0);
        $this->family_week_registration_ids['jozef_de_backer_week_0'] = DB::table('family_week_registrations')->insertGetId([
            "family_id" => $family_jozef_de_backer->id,
            "week_id" => $week0->id,
            "tariff_id" => $family_jozef_de_backer->tariff_id
        ]);
        $child_josephine_janssens = $this->children('josephine_janssens');
        $this->child_family_week_registration_ids['josephine_janssens_week0'] = DB::table('child_family_week_registrations')->insertGetId([
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
