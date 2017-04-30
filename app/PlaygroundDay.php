<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    public static function getPlaygroundDayForDate($date)
    {
        $week = Week::query()
            ->whereDate('first_day_of_week', '<=', $date->format('Y-m-d'))
            ->orderByDesc('first_day_of_week')
            ->first();
        if (!$week)
            return null;
        $interval = $date->diff(\DateTime::createFromFormat('Y-m-d', $week->first_day_of_week));
        Log::info("Interval days: " . $interval->days);
        $week_day = WeekDay::query()
            ->where('days_offset', '=', $interval->days)
            ->first();
        if (!$week_day)
            return null;
        return $week->playground_days()->where('week_day_id', '=', $week_day->id)->first();

    }
}
