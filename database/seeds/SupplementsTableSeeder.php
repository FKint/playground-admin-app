<?php

use Illuminate\Database\Seeder;

class SupplementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('supplements')->insert([
            'name' => "IJsje",
            'price' => '0.50'
        ]);
    }
}
