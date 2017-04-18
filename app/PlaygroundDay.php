<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlaygroundDay extends Model
{
    public function week()
    {
        return $this->belongsTo(Week::class);
    }

    public function week_day()
    {
        return $this->belongsTo(WeekDay::class);
    }

    public function date()
    {
        $interval = new \DateInterval('P' . $this->week_day->days_offset . 'D');
        return (new \DateTimeImmutable($this->week->first_day_of_week))->add($interval);
    }
}
