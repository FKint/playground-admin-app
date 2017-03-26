<?php

use Illuminate\Database\Seeder;

class ChildrenTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
}
