<?php

namespace Database\Seeders;

class TestDataSeeder extends InitialDataSeeder
{
    protected function seed_dates()
    {
        $year_2017_id = DB::table('years')->insertGetId([
            'year' => 2017,
        ]);
        $week_day_names = ['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag'];
        $holidays = ['2017-07-21'];
        for ($i = 0; $i < 5; ++$i) {
            $this->week_day_ids[] = DB::table('week_days')->insertGetId([
                'days_offset' => $i,
                'name' => $week_day_names[$i],
            ]);
        }
        $monday = (new DateTimeImmutable())->setDate(2017, 6, 12);
        $day = new DateInterval('P1D');
        $week = new DateInterval('P1W');
        for ($i = 0; $i < 9; ++$i) {
            $this->week_ids[$i] = DB::table('weeks')->insertGetId([
                'year_id' => $year_2017_id,
                'week_number' => 1 + $i,
                'first_day_of_week' => $monday,
            ]);
            $week_day = $monday;
            for ($j = 0; $j < count($this->week_day_ids); ++$j) {
                if (!in_array($week_day->format('Y-m-d'), $holidays)) {
                    DB::table('playground_days')->insert([
                        'week_id' => $this->week_ids[$i],
                        'week_day_id' => $this->week_day_ids[$j],
                    ]);
                    $week_day = $week_day->add($day);
                }
            }
            $monday = $monday->add($week);
        }
    }
}
