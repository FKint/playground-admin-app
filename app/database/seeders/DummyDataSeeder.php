<?php

namespace Database\Seeders;

use App\Child;

class DummyDataSeeder extends InitialDataSeeder
{
    protected $families = [];
    protected $children = [];
    protected $family_week_registrations = [];
    protected $child_families = [];
    protected $child_family_week_registrations = [];
    protected $child_family_day_registrations = [];
    protected $activity_list;

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
        return \App\Family::findOrFail($this->families[$family_id]->id);
    }

    protected function children($child_id)
    {
        return Child::findOrFail($this->children[$child_id]->id);
    }

    protected function child_families($child_family_id)
    {
        return \App\ChildFamily::findOrFail($this->child_families[$child_family_id]->id);
    }

    protected function seed_children()
    {
        $this->children['josephine_janssens'] = Child::factory()
            ->for($this->year)
            ->for($this->toddlers)
            ->create([
                'first_name' => 'Josephine',
                'last_name' => 'Janssens',
                'birth_year' => 2013,
                'remarks' => 'First kid in the DB!',
            ]);
        $this->children['eefje_janssens'] = Child::factory()
            ->for($this->year)
            ->for($this->toddlers)
            ->create([
                'first_name' => 'Eefje',
                'last_name' => 'Janssens',
                'birth_year' => 2013,
                'remarks' => '!',
            ]);
        $this->children['karen_duyck'] = Child::factory()
            ->for($this->year)
            ->for($this->middle_group)
            ->create([
                'first_name' => 'Karen',
                'last_name' => 'Duyck',
                'birth_year' => 2008,
                'remarks' => '',
            ]);
        $this->children['tom_maes'] = Child::factory()
            ->for($this->year)
            ->for($this->teenagers)
            ->create([
                'first_name' => 'Tom',
                'last_name' => 'Maes',
                'birth_year' => 2006,
                'remarks' => '',
            ]);
        $this->children['erika_van_leemhuyzen'] = Child::factory()
            ->for($this->year)
            ->for($this->middle_group)
            ->create([
                'first_name' => 'Erika',
                'last_name' => 'Van Leemhuyzen',
                'birth_year' => 2009,
                'remarks' => '',
            ]);
        $this->children['tim_beert'] = Child::factory()
            ->for($this->year)
            ->for($this->middle_group)
            ->create([
                'first_name' => 'Tim',
                'last_name' => 'Beert',
                'birth_year' => 2010,
                'remarks' => '',
            ]);
        $this->children['tom_beert'] = Child::factory()
            ->for($this->year)
            ->for($this->middle_group)
            ->create([
                'first_name' => 'Tom',
                'last_name' => 'Beert',
                'birth_year' => 2011,
                'remarks' => '',
            ]);
        $this->children['jonas_beert'] = Child::factory()
            ->for($this->year)
            ->for($this->middle_group)
            ->create([
                'first_name' => 'Jonas',
                'last_name' => 'Beert',
                'birth_year' => 2007,
                'remarks' => '',
            ]);
        $this->children['lieven_devriendt'] = Child::factory()
            ->for($this->year)
            ->for($this->middle_group)
            ->create([
                'first_name' => 'Lieven',
                'last_name' => 'Devriendt',
                'birth_year' => 2006,
                'remarks' => '',
            ]);
        $this->children['ann_vanovertveldt'] = Child::factory()
            ->for($this->year)
            ->for($this->middle_group)
            ->create([
                'first_name' => 'Ann',
                'last_name' => 'Vanovertveldt',
                'birth_year' => 2011,
                'remarks' => '',
            ]);
        $this->children['johan_claeys'] = Child::factory()
            ->for($this->year)
            ->for($this->toddlers)
            ->create([
                'first_name' => 'Johan',
                'last_name' => 'Claeys',
                'birth_year' => 2013,
                'remarks' => '',
            ]);
        $this->children['driss_de_koninck'] = Child::factory()
            ->for($this->year)
            ->for($this->toddlers)
            ->create([
                'first_name' => 'Driss',
                'last_name' => 'De Koninck',
                'birth_year' => 2012,
                'remarks' => '',
            ]);
        $this->children['veerle_christy'] = Child::factory()
            ->for($this->year)
            ->for($this->middle_group)
            ->create([
                'first_name' => 'Veerle',
                'last_name' => 'Christy',
                'birth_year' => 2010,
                'remarks' => '',
            ]);
    }

    protected function seed_child_families()
    {
        $normal_tariff = \App\Tariff::where('name', '=', 'Normaal')->firstOrFail();
        $social_tariff = \App\Tariff::where('name', '=', 'Sociaal')->firstOrFail();
        $this->families['jozef_de_backer'] = \App\Family::factory()
            ->for($this->year)
            ->for($normal_tariff)
            ->create([
                'year_id' => $this->year->id,
                'guardian_first_name' => 'Jozef',
                'guardian_last_name' => 'De Backer',
                'tariff_id' => $normal_tariff->id,
                'remarks' => 'First family',
                'contact' => '1207',
            ]);
        $child = Child::where('first_name', '=', 'Josephine')->firstOrFail();
        $this->child_families['jozef_de_backer_josephine'] = \App\ChildFamily::factory()
            ->for($this->year)
            ->for($this->families['jozef_de_backer'])
            ->for($child)
            ->create();
        $child = Child::where('first_name', '=', 'Eefje')->firstOrFail();
        $this->child_families['jozef_de_backer_eefje'] = \App\ChildFamily::factory()
            ->for($this->year)
            ->for($this->families['jozef_de_backer'])
            ->for($child)
            ->create();
        \App\Family::factory()
            ->for($this->year)
            ->for($normal_tariff)
            ->create([
                'guardian_first_name' => 'Heidi',
                'guardian_last_name' => 'De Vriendt',
                'remarks' => 'Second family',
                'contact' => '999',
            ]);
        \App\Family::factory()
            ->for($this->year)
            ->for($normal_tariff)
            ->create([
                'guardian_first_name' => 'Jonas',
                'guardian_last_name' => 'De Keukeleire',
                'remarks' => '',
                'contact' => '112',
            ]);
        \App\Family::factory()
            ->for($this->year)
            ->for($normal_tariff)
            ->create([
                'guardian_first_name' => 'Erik',
                'guardian_last_name' => 'Anthonissen',
                'remarks' => '',
                'contact' => '',
            ]);
        \App\Family::factory()
            ->for($this->year)
            ->for($normal_tariff)
            ->create([
                'guardian_first_name' => 'Lieve',
                'guardian_last_name' => 'Bouckaert',
                'remarks' => '',
                'contact' => '',
            ]);
        \App\Family::factory()
            ->for($this->year)
            ->for($social_tariff)
            ->create([
                'guardian_first_name' => 'Ann',
                'guardian_last_name' => 'Meert',
                'remarks' => '',
                'contact' => '',
            ]);
    }

    protected function seed_activity_lists()
    {
        $this->activity_list = \App\ActivityList::factory()->for($this->year)->create([
            'name' => "Zwembad O'Town",
            'date' => '2018-07-06',
            'show_on_attendance_form' => true,
            'show_on_dashboard' => true,
            'price' => '1.75',
        ]);
        \DB::table('activity_list_child_families')->insert([
            'child_family_id' => $this->child_families('jozef_de_backer_josephine')->id,
            'activity_list_id' => $this->activity_list->id,
            'year_id' => $this->year->id,
        ]);
        \DB::table('activity_list_child_families')->insert([
            'child_family_id' => $this->child_families('jozef_de_backer_eefje')->id,
            'activity_list_id' => $this->activity_list->id,
            'year_id' => $this->year->id,
        ]);
    }

    protected function seed_registrations()
    {
        $family_jozef_de_backer = $this->families('jozef_de_backer');
        $week0 = $this->weeks($this->weeks[0]->id);
        $this->family_week_registrations['jozef_de_backer_week_0'] = \DB::table('family_week_registrations')->insertGetId([
            'family_id' => $family_jozef_de_backer->id,
            'week_id' => $week0->id,
            'tariff_id' => $family_jozef_de_backer->tariff_id,
            'year_id' => $this->year->id,
        ]);
        $child_josephine_janssens = $this->children('josephine_janssens');
        $this->child_family_week_registrations['josephine_janssens_week0'] = \DB::table('child_family_week_registrations')->insertGetId([
            'child_id' => $child_josephine_janssens->id,
            'family_id' => $family_jozef_de_backer->id,
            'week_id' => $week0->id,
            'whole_week_price' => false,
            'year_id' => $this->year->id,
        ]);
        $week_day0 = $this->week_days[0];
        $this->child_family_day_registrations['josephine_janssens_week0_day0'] = \DB::table('child_family_day_registrations')->insert([
            'child_id' => $child_josephine_janssens->id,
            'family_id' => $family_jozef_de_backer->id,
            'week_id' => $week0->id,
            'week_day_id' => $week_day0->id,
            'day_part_id' => $this->whole_day->id,
            'attended' => true,
            'age_group_id' => $this->toddlers->id,
            'year_id' => $this->year->id,
        ]);
        $week_day1 = $this->week_days[1];
        $this->child_family_day_registrations['josephine_janssens_week0_day1'] = \DB::table('child_family_day_registrations')->insert([
            'child_id' => $child_josephine_janssens->id,
            'family_id' => $family_jozef_de_backer->id,
            'week_id' => $week0->id,
            'week_day_id' => $week_day1->id,
            'day_part_id' => $this->whole_day->id,
            'attended' => true,
            'age_group_id' => $this->toddlers->id,
            'year_id' => $this->year->id,
        ]);
        $child_eefje_janssens = $this->children('eefje_janssens');
        $this->child_family_week_registrations['eefje_janssens_week0'] = \DB::table('child_family_week_registrations')->insert([
            'child_id' => $child_eefje_janssens->id,
            'family_id' => $family_jozef_de_backer->id,
            'week_id' => $week0->id,
            'whole_week_price' => true,
            'year_id' => $this->year->id,
        ]);
        foreach ($week0->playground_days as $playground_day) {
            $this->child_family_day_registrations['eefje_janssens_week0_day'.$playground_day->week_day->days_offset] = \DB::table('child_family_day_registrations')->insert([
                'child_id' => $child_eefje_janssens->id,
                'family_id' => $family_jozef_de_backer->id,
                'week_id' => $week0->id,
                'week_day_id' => $playground_day->week_day_id,
                'day_part_id' => $this->whole_day->id,
                'attended' => false,
                'age_group_id' => $this->toddlers->id,
                'year_id' => $this->year->id,
            ]);
        }

        \DB::table('transactions')->insertGetId([
            'amount_paid' => 30.5,
            'amount_expected' => 30.5,
            'family_id' => $family_jozef_de_backer->id,
            'admin_session_id' => $this->first_admin_session->id,
            'created_at' => \Carbon\Carbon::now(),
            'year_id' => $this->year->id,
        ]);
    }

    protected function seed_users()
    {
        \App\User::factory()->for($this->organization)->create([
            'email' => 'admin@playground.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
