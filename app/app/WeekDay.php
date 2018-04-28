<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeekDay extends Model
{
    //

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function make_copy($year)
    {
        $new_week_day = $this->replicate();
        $new_week_day->year()->associate($year);
        return $new_week_day;
    }
}
