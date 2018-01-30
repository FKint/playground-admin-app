<?php

namespace App;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['year'];

    /**
     * Get all weeks in this year.
     */
    public function weeks()
    {
        return $this->hasMany(Week::class);
    }

    public function getActiveAdminSession()
    {
        return $this->admin_sessions()->whereNull('session_end')->firstOrFail();
    }

    public function admin_sessions()
    {
        return $this->hasMany(AdminSession::class);
    }

    public function count_registrations_for_age_group($age_group)
    {
        return $this->weeks()
            ->join('child_family_day_registrations', 'weeks.id', '=', 'child_family_day_registrations.week_id')
            ->where('child_family_day_registrations.age_group_id', $age_group->id)
            ->count();
    }

    public function count_registrations()
    {
        return $this->weeks()
            ->join('child_family_day_registrations', 'weeks.id', '=', 'child_family_day_registrations.week_id')
            ->count();
    }

    public function playground_days()
    {
        return $this->hasManyThrough(PlaygroundDay::class, Week::class, 'year_id', 'week_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function getAllAgeGroupsById()
    {
        return $this->age_groups->mapWithKeys(function ($group) {
            return [$group->id => $group->full_abbreviation_and_name()];
        })->toArray();
    }

    public function getAllTariffsById()
    {
        return $this->tariffs->mapWithKeys(function ($tariff) {
            return [$tariff->id => $tariff->full_abbreviation_and_name()];
        });
    }

    public function day_parts()
    {
        return $this->hasMany(DayPart::class);
    }

    public function age_groups()
    {
        return $this->hasMany(AgeGroup::class);
    }

    public function tariffs()
    {
        return $this->hasMany(Tariff::class);
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    public function families()
    {
        return $this->hasMany(Family::class);
    }

    public function activity_lists()
    {
        return $this->hasMany(ActivityList::class);
    }

    public function child_families()
    {
        return $this->hasMany(ChildFamily::class);
    }

    public function week_days()
    {
        return $this->hasMany(WeekDay::class);
    }

    public function getPlaygroundDayForDate(DateTimeImmutable $date)
    {
        $week = $this->weeks()
            ->whereDate('first_day_of_week', '<=', $date->format('Y-m-d'))
            ->orderByDesc('first_day_of_week')
            ->first();
        if (!$week)
            return null;
        $interval = $date->diff(\DateTime::createFromFormat('Y-m-d', $week->first_day_of_week));
        $week_day = $this->week_days()
            ->where('days_offset', '=', $interval->days)
            ->first();
        if (!$week_day)
            return null;
        return $week->playground_days()->where('week_day_id', '=', $week_day->id)->first();
    }

    public function supplements()
    {
        return $this->hasMany(Supplement::class);
    }
}
