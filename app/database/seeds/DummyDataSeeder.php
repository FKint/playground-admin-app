<?php

use App\Child;

class DummyDataSeeder extends InitialDataSeeder
{
    protected $family_ids = [];
    protected $child_ids = [];
    protected $family_week_registration_ids = [];
    protected $child_family_ids = [];
    protected $child_family_week_registration_ids = [];
    protected $child_family_day_registration_ids = [];
    protected $activity_list_id;

    /**
     * Run the database seeds.
     *
     * @throws Exception
     */
    public function run()
    {
        parent::run();
        $this->seed_children();
        $this->seed_child_families();
        $this->seed_activity_lists();
        $this->seed_registrations();
        $this->seed_users();
    }

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
        $this->child_ids['josephine_janssens'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Josephine',
            'last_name' => 'Janssens',
            'birth_year' => 2013,
            'age_group_id' => $this->toddlers_id,
            'remarks' => 'First kid in the DB!',
        ])->id;
        $this->child_ids['eefje_janssens'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Eefje',
            'last_name' => 'Janssens',
            'birth_year' => 2013,
            'age_group_id' => $this->toddlers_id,
            'remarks' => '!',
        ])->id;
        $this->child_ids['karen_duyck'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Karen',
            'last_name' => 'Duyck',
            'birth_year' => 2008,
            'age_group_id' => $this->middle_group_id,
            'remarks' => '',
        ])->id;
        $this->child_ids['tom_maes'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Tom',
            'last_name' => 'Maes',
            'birth_year' => 2006,
            'age_group_id' => $this->teenagers_id,
            'remarks' => '',
        ])->id;
        $this->child_ids['erika_van_leemhuyzen'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Erika',
            'last_name' => 'Van Leemhuyzen',
            'birth_year' => 2009,
            'age_group_id' => $this->middle_group_id,
            'remarks' => '',
        ])->id;
        $this->child_ids['tim_beert'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Tim',
            'last_name' => 'Beert',
            'birth_year' => 2010,
            'age_group_id' => $this->middle_group_id,
            'remarks' => '',
        ])->id;
        $this->child_ids['tom_beert'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Tom',
            'last_name' => 'Beert',
            'birth_year' => 2011,
            'age_group_id' => $this->middle_group_id,
            'remarks' => '',
        ])->id;
        $this->child_ids['jonas_beert'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Jonas',
            'last_name' => 'Beert',
            'birth_year' => 2007,
            'age_group_id' => $this->middle_group_id,
            'remarks' => '',
        ])->id;
        $this->child_ids['lieven_devriendt'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Lieven',
            'last_name' => 'Devriendt',
            'birth_year' => 2006,
            'age_group_id' => $this->middle_group_id,
            'remarks' => '',
        ])->id;
        $this->child_ids['ann_vanovertveldt'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Ann',
            'last_name' => 'Vanovertveldt',
            'birth_year' => 2011,
            'age_group_id' => $this->middle_group_id,
            'remarks' => '',
        ])->id;
        $this->child_ids['johan_claeys'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Johan',
            'last_name' => 'Claeys',
            'birth_year' => 2013,
            'age_group_id' => $this->toddlers_id,
            'remarks' => '',
        ])->id;
        $this->child_ids['driss_de_koninck'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Driss',
            'last_name' => 'De Koninck',
            'birth_year' => 2012,
            'age_group_id' => $this->toddlers_id,
            'remarks' => '',
        ])->id;
        $this->child_ids['veerle_christy'] = factory(Child::class)->create([
            'year_id' => $this->year->id,
            'first_name' => 'Veerle',
            'last_name' => 'Christy',
            'birth_year' => 2010,
            'age_group_id' => $this->middle_group_id,
            'remarks' => '',
        ])->id;
    }

    protected function seed_child_families()
    {
        $normal_tariff = \App\Tariff::where('name', '=', 'Normaal')->firstOrFail();
        $social_tariff = \App\Tariff::where('name', '=', 'Sociaal')->firstOrFail();
        $this->family_ids['jozef_de_backer'] = factory(\App\Family::class)->create([
            'year_id' => $this->year->id,
            'guardian_first_name' => 'Jozef',
            'guardian_last_name' => 'De Backer',
            'tariff_id' => $normal_tariff->id,
            'remarks' => 'First family',
            'contact' => '1207',
        ])->id;
        $child = \App\Child::where('first_name', '=', 'Josephine')->firstOrFail();
        $this->child_family_ids['jozef_de_backer_josephine'] = factory(\App\ChildFamily::class)->create([
            'year_id' => $this->year->id,
            'family_id' => $this->family_ids['jozef_de_backer'],
            'child_id' => $child->id,
        ])->id;
        $child = \App\Child::where('first_name', '=', 'Eefje')->firstOrFail();
        $this->child_family_ids['jozef_de_backer_eefje'] = factory(\App\ChildFamily::class)->create([
            'year_id' => $this->year->id,
            'family_id' => $this->family_ids['jozef_de_backer'],
            'child_id' => $child->id,
        ])->id;
        factory(\App\Family::class)->create([
            'year_id' => $this->year->id,
            'guardian_first_name' => 'Heidi',
            'guardian_last_name' => 'De Vriendt',
            'tariff_id' => $normal_tariff->id,
            'remarks' => 'Second family',
            'contact' => '999',
        ]);
        factory(\App\Family::class)->create([
            'year_id' => $this->year->id,
            'guardian_first_name' => 'Jonas',
            'guardian_last_name' => 'De Keukeleire',
            'tariff_id' => $normal_tariff->id,
            'remarks' => '',
            'contact' => '112',
        ]);
        factory(\App\Family::class)->create([
            'year_id' => $this->year->id,
            'guardian_first_name' => 'Erik',
            'guardian_last_name' => 'Anthonissen',
            'tariff_id' => $normal_tariff->id,
            'remarks' => '',
            'contact' => '',
        ]);
        factory(\App\Family::class)->create([
            'year_id' => $this->year->id,
            'guardian_first_name' => 'Lieve',
            'guardian_last_name' => 'Bouckaert',
            'tariff_id' => $normal_tariff->id,
            'remarks' => '',
            'contact' => '',
        ]);
        factory(\App\Family::class)->create([
            'year_id' => $this->year->id,
            'guardian_first_name' => 'Ann',
            'guardian_last_name' => 'Meert',
            'tariff_id' => $social_tariff->id,
            'remarks' => '',
            'contact' => '',
        ]);
    }

    protected function seed_activity_lists()
    {
        $this->activity_list_id = factory(\App\ActivityList::class)->create([
            'year_id' => $this->year->id,
            'name' => "Zwembad O'Town",
            'date' => '2018-07-06',
            'show_on_attendance_form' => true,
            'show_on_dashboard' => true,
            'price' => '1.75',
        ])->id;
        DB::table('activity_list_child_families')->insert([
            'child_family_id' => $this->child_families('jozef_de_backer_josephine')->id,
            'activity_list_id' => $this->activity_list_id,
            'year_id' => $this->year->id,
        ]);
        DB::table('activity_list_child_families')->insert([
            'child_family_id' => $this->child_families('jozef_de_backer_eefje')->id,
            'activity_list_id' => $this->activity_list_id,
            'year_id' => $this->year->id,
        ]);
    }

    protected function seed_registrations()
    {
        $family_jozef_de_backer = $this->families('jozef_de_backer');
        $week0 = $this->weeks($this->week_ids[0]);
        $this->family_week_registration_ids['jozef_de_backer_week_0'] = DB::table('family_week_registrations')->insertGetId([
            'family_id' => $family_jozef_de_backer->id,
            'week_id' => $week0->id,
            'tariff_id' => $family_jozef_de_backer->tariff_id,
            'year_id' => $this->year->id,
        ]);
        $child_josephine_janssens = $this->children('josephine_janssens');
        $this->child_family_week_registration_ids['josephine_janssens_week0'] = DB::table('child_family_week_registrations')->insertGetId([
            'child_id' => $child_josephine_janssens->id,
            'family_id' => $family_jozef_de_backer->id,
            'week_id' => $week0->id,
            'whole_week_price' => false,
            'year_id' => $this->year->id,
        ]);
        $week_day0 = $this->week_days(0);
        $this->child_family_day_registration_ids['josephine_janssens_week0_day0'] = DB::table('child_family_day_registrations')->insert([
            'child_id' => $child_josephine_janssens->id,
            'family_id' => $family_jozef_de_backer->id,
            'week_id' => $week0->id,
            'week_day_id' => $week_day0->id,
            'day_part_id' => $this->whole_day_id,
            'attended' => true,
            'age_group_id' => $this->toddlers_id,
            'year_id' => $this->year->id,
        ]);
        $week_day1 = $this->week_days(1);
        $this->child_family_day_registration_ids['josephine_janssens_week0_day1'] = DB::table('child_family_day_registrations')->insert([
            'child_id' => $child_josephine_janssens->id,
            'family_id' => $family_jozef_de_backer->id,
            'week_id' => $week0->id,
            'week_day_id' => $week_day1->id,
            'day_part_id' => $this->whole_day_id,
            'attended' => true,
            'age_group_id' => $this->toddlers_id,
            'year_id' => $this->year->id,
        ]);
        $child_eefje_janssens = $this->children('eefje_janssens');
        $this->child_family_week_registration_ids['eefje_janssens_week0'] = DB::table('child_family_week_registrations')->insert([
            'child_id' => $child_eefje_janssens->id,
            'family_id' => $family_jozef_de_backer->id,
            'week_id' => $week0->id,
            'whole_week_price' => true,
            'year_id' => $this->year->id,
        ]);
        foreach ($week0->playground_days as $playground_day) {
            $this->child_family_day_registration_ids['eefje_janssens_week0_day'.$playground_day->week_day->days_offset] = DB::table('child_family_day_registrations')->insert([
                'child_id' => $child_eefje_janssens->id,
                'family_id' => $family_jozef_de_backer->id,
                'week_id' => $week0->id,
                'week_day_id' => $playground_day->week_day_id,
                'day_part_id' => $this->whole_day_id,
                'attended' => false,
                'age_group_id' => $this->toddlers_id,
                'year_id' => $this->year->id,
            ]);
        }

        DB::table('transactions')->insertGetId([
            'amount_paid' => 30.5,
            'amount_expected' => 30.5,
            'family_id' => $family_jozef_de_backer->id,
            'admin_session_id' => $this->first_admin_session_id,
            'created_at' => \Carbon\Carbon::now(),
            'year_id' => $this->year->id,
        ]);
    }

    protected function seed_users()
    {
        factory(\App\User::class)->create([
            'organization_id' => $this->organization->id,
            'email' => 'admin@playground.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
