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

    public function year()
    {
        return $this->belongsTo(Year::class);
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

    public function child_family_day_registrations()
    {
        return ChildFamilyDayRegistration::query()
            ->where([['week_id', $this->week_id], ['week_day_id', $this->week_day_id]]);
    }

    public function count_supplements_for_age_group($supplement, $age_group)
    {
        $result = $this->child_family_day_registrations()
            ->where('age_group_id', $age_group->id)
            ->join('child_family_day_registration_supplements', 'child_family_day_registrations.id', '=', 'child_family_day_registration_supplements.child_family_day_registration_id')
            ->where('child_family_day_registration_supplements.supplement_id', $supplement->id)
            ->get();
        return count($result);
    }

    public function count_supplements($supplement)
    {
        return 0;
    }

    public function count_registrations_for_age_group($age_group)
    {
        $result = $this->child_family_day_registrations()
            ->where('age_group_id', $age_group->id)
            ->count();
        return $result;
    }

    public function count_registrations()
    {
        return $this->child_family_day_registrations()->count();
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
