<?php

use Illuminate\Database\Seeder;

class AgeGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('age_groups')->insert([
            'name' => 'Kleuters',
            'abbreviation' => 'KLS',
            'start_date' => (new DateTime())->setDate(2011, 1, 1),
            'end_date' => (new DateTime())->setDate(2014, 1, 1)
        ]);
        DB::table('age_groups')->insert([
            'name' => 'Grote',
            'abbreviation' => '6-12',
            'start_date' => (new DateTime())->setDate(2005, 1, 1),
            'end_date' => (new DateTime())->setDate(2006, 1, 1)
        ]);
        DB::table('age_groups')->insert([
            'name' => 'Tieners',
            'abbreviation' => '12+',
            'start_date' => (new DateTime())->setDate(2004, 1, 1),
            'end_date' => (new DateTime())->setDate(2006, 1, 1)
        ]);
    }
}
